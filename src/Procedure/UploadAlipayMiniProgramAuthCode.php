<?php

namespace AlipayMiniProgramBundle\Procedure;

use Alipay\OpenAPISDK\Api\AlipaySystemOauthApi;
use Alipay\OpenAPISDK\Model\AlipaySystemOauthTokenModel;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use AlipayMiniProgramBundle\Repository\UserRepository;
use AlipayMiniProgramBundle\Response\AlipaySystemOauthTokenResponse;
use AlipayMiniProgramBundle\Service\UserService;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\AccessTokenBundle\Service\AccessTokenService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcParams;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

/**
 * @see https://opendocs.alipay.com/open/02xtl8?pathHash=e39a95b3
 */
#[MethodDoc(summary: '支付宝小程序上传授权码')]
#[MethodTag(name: '支付宝小程序')]
#[MethodExpose(method: 'UploadAlipayMiniProgramAuthCode')]
#[Log]
class UploadAlipayMiniProgramAuthCode extends LockableProcedure
{
    /**
     * 小程序的 AppID
     */
    #[MethodParam(description: '小程序的 AppID')]
    public string $appId = '';

    /**
     * 授权范围
     */
    #[MethodParam(description: '授权范围，auth_base 或 auth_user')]
    #[Assert\NotNull]
    public string $scope;

    /**
     * 授权码
     */
    #[MethodParam(description: '授权码')]
    #[Assert\NotNull]
    public string $authCode;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MiniProgramRepository $miniProgramRepository,
        private readonly UserRepository $userRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly RequestStack $requestStack,
        private readonly UserService $userService,
        private readonly AccessTokenService $accessTokenService,
    ) {
    }

    public function execute(): array
    {
        $this->validateParameters();
        $miniProgram = $this->getMiniProgram();

        try {
            $response = $this->getAlipayAccessToken($miniProgram);
            $user = $this->findOrCreateUser($miniProgram, $response);
            $this->dispatchUserInfoUpdate($user);
            $authCodeEntity = $this->createAuthCodeEntity($user, $response);
            $token = $this->createBusinessToken($user);

            return $this->buildResponse($user, $authCodeEntity, $token);
        } catch (TimeoutException $exception) {
            throw new ApiException('支付宝接口超时，请稍后重试', 0, previous: $exception);
        } catch (UniqueConstraintViolationException $exception) {
            $this->getLockLogger()->error('授权失败:' . $exception->getMessage());
            throw new ApiException('请返回重试', previous: $exception);
        }
    }

    private function validateParameters(): void
    {
        if (!in_array($this->scope, [AlipayAuthScope::AUTH_BASE->value, AlipayAuthScope::AUTH_USER->value], true)) {
            throw new ApiException('无效的授权范围');
        }
    }

    private function getMiniProgram(): MiniProgram
    {
        $miniProgram = $this->miniProgramRepository->findOneBy(['appId' => $this->appId]);
        if (null === $miniProgram) {
            throw new ApiException('未找到小程序配置');
        }

        return $miniProgram;
    }

    private function getAlipayAccessToken(MiniProgram $miniProgram): AlipaySystemOauthTokenResponse
    {
        $apiInstance = new AlipaySystemOauthApi(new Client());
        $alipayConfig = $this->buildAlipayConfig($miniProgram);
        $apiInstance->setAlipayConfigUtil(new AlipayConfigUtil($alipayConfig));

        $tokenModel = new AlipaySystemOauthTokenModel();
        $tokenModel->setGrantType('authorization_code');
        $tokenModel->setCode($this->authCode);

        try {
            $response = $apiInstance->token($tokenModel);

            // 将响应转换为 stdClass
            if (method_exists($response, 'toArray')) {
                $responseData = (object) $response->toArray();
            } else {
                $jsonString = json_encode($response);
                if (false === $jsonString) {
                    throw new ApiException('无法序列化响应数据');
                }
                $responseData = (object) json_decode($jsonString, true);
            }

            return new AlipaySystemOauthTokenResponse($responseData);
        } catch (\Throwable $exception) {
            throw new ApiException(sprintf('获取访问令牌失败：%s', $exception->getMessage()), previous: $exception);
        }
    }

    private function buildAlipayConfig(MiniProgram $miniProgram): AlipayConfig
    {
        $alipayConfig = new AlipayConfig();
        $alipayConfig->setAppId($miniProgram->getAppId());
        $alipayConfig->setPrivateKey($miniProgram->getPrivateKey());
        $alipayConfig->setAlipayPublicKey($miniProgram->getAlipayPublicKey());

        $encryptKey = $miniProgram->getEncryptKey();
        if (null !== $encryptKey) {
            $alipayConfig->setEncryptKey($encryptKey);
        }

        return $alipayConfig;
    }

    private function findOrCreateUser(MiniProgram $miniProgram, AlipaySystemOauthTokenResponse $response): User
    {
        $userId = $response->getUserId();
        if (null === $userId) {
            throw new ApiException('用户ID不能为空');
        }

        $user = $this->userRepository->findOneBy(['openId' => $userId]);
        if (null === $user) {
            $user = new User();
            $user->setMiniProgram($miniProgram);
            $user->setOpenId($userId);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function dispatchUserInfoUpdate(User $user): void
    {
        if ($this->scope === AlipayAuthScope::AUTH_USER->value) {
            $userId = $user->getId();
            if (null !== $userId) {
                $message = new UpdateUserInfoMessage($userId, $this->authCode);
                $this->messageBus->dispatch($message);
            }
        }
    }

    private function createAuthCodeEntity(User $user, AlipaySystemOauthTokenResponse $response): AuthCode
    {
        $userId = $response->getUserId();
        $accessToken = $response->getAccessToken();
        $refreshToken = $response->getRefreshToken();

        if (null === $userId) {
            throw new ApiException('用户ID不能为空');
        }
        if (null === $accessToken) {
            throw new ApiException('访问令牌不能为空');
        }
        if (null === $refreshToken) {
            throw new ApiException('刷新令牌不能为空');
        }

        $authCodeEntity = new AuthCode();
        $authCodeEntity->setAlipayUser($user);
        $authCodeEntity->setAuthCode($this->authCode);
        $authCodeEntity->setScope(AlipayAuthScope::from($this->scope));
        $authCodeEntity->setOpenId($userId);
        $authCodeEntity->setUserId($userId);
        $authCodeEntity->setAccessToken($accessToken);
        $authCodeEntity->setRefreshToken($refreshToken);
        // 处理 expires_in 的边界情况：null或负值时设置为1（最小有效值）
        $expiresIn = $response->getExpiresIn();
        $authCodeEntity->setExpiresIn((null !== $expiresIn && $expiresIn > 0) ? $expiresIn : 1);

        $reExpiresIn = $response->getReExpiresIn();
        $authCodeEntity->setReExpiresIn((null !== $reExpiresIn && $reExpiresIn > 0) ? $reExpiresIn : 1);
        $authStart = $response->getAuthStart();
        if (null !== $authStart) {
            $authCodeEntity->setAuthStart(CarbonImmutable::createFromTimestamp((int) $authStart, date_default_timezone_get())->toDateTimeImmutable());
        }
        $authCodeEntity->setCreatedFromIp($this->requestStack->getMainRequest()?->getClientIp());

        $this->entityManager->persist($user);
        $this->entityManager->persist($authCodeEntity);
        $this->entityManager->flush();

        return $authCodeEntity;
    }

    private function createBusinessToken(User $user): string
    {
        $bizUser = $this->userService->getBizUser($user);

        return $this->accessTokenService->createToken($bizUser);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildResponse(User $user, AuthCode $authCodeEntity, string $token): array
    {
        $authStart = $authCodeEntity->getAuthStart();
        $result = [
            'userId' => $user->getId(),
            'openId' => $user->getOpenId(),
            'accessToken' => $authCodeEntity->getAccessToken(),
            'refreshToken' => $authCodeEntity->getRefreshToken(),
            'expiresIn' => $authCodeEntity->getExpiresIn(),
            'reExpiresIn' => $authCodeEntity->getReExpiresIn(),
            'authStart' => $authStart?->format('Y-m-d H:i:s'),
            'jwt' => $token,
            'phone' => $this->userService->getAllPhones($user),
        ];

        if ($this->scope === AlipayAuthScope::AUTH_USER->value) {
            $result['userInfo'] = [
                'nickName' => $user->getNickName(),
                'avatar' => $user->getAvatar(),
                'province' => $user->getProvince(),
                'city' => $user->getCity(),
                'gender' => $user->getGender()?->value,
            ];
        }

        return $result;
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        $authCode = $params->get('authCode');
        if (!is_string($authCode)) {
            return null;
        }

        return [
            'UploadAlipayMiniProgramAuthCode' . $authCode,
        ];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            throw new ApiException('请求参数不能为空');
        }

        $authCode = $params->get('authCode');
        if (!is_string($authCode)) {
            throw new ApiException('授权码必须是字符串类型');
        }

        return 'UploadAlipayMiniProgramAuthCode-idempotent-' . $authCode;
    }
}
