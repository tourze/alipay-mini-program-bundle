<?php

namespace AlipayMiniProgramBundle\Procedure;

use AccessTokenBundle\Service\AccessTokenService;
use Alipay\OpenAPISDK\Api\AlipaySystemOauthApi;
use Alipay\OpenAPISDK\Model\AlipaySystemOauthTokenModel;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use AlipayMiniProgramBundle\Repository\UserRepository;
use AlipayMiniProgramBundle\Service\UserService;
use Carbon\Carbon;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
#[MethodDoc('支付宝小程序上传授权码')]
#[MethodTag('支付宝小程序')]
#[MethodExpose('UploadAlipayMiniProgramAuthCode')]
#[Log]
class UploadAlipayMiniProgramAuthCode extends LockableProcedure
{
    /**
     * 小程序的 AppID
     */
    #[MethodParam('小程序的 AppID')]
    public string $appId = '';

    /**
     * 授权范围
     */
    #[MethodParam('授权范围，auth_base 或 auth_user')]
    #[Assert\NotNull]
    public string $scope;

    /**
     * 授权码
     */
    #[MethodParam('授权码')]
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
        // 校验参数
        if (!in_array($this->scope, [AlipayAuthScope::AUTH_BASE->value, AlipayAuthScope::AUTH_USER->value])) {
            throw new ApiException('无效的授权范围');
        }

        // 获取小程序配置
        $miniProgram = $this->miniProgramRepository->findOneBy(['appId' => $this->appId]);
        if (!$miniProgram) {
            throw new ApiException('未找到小程序配置');
        }

        try {
            // 使用授权码换取访问令牌
            $apiInstance = new AlipaySystemOauthApi(
                // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                // This is optional, `GuzzleHttp\Client` will be used as default.
                new \GuzzleHttp\Client()
            );

            // 初始化alipay参数
            $alipayConfig = new AlipayConfig();
            $alipayConfig->setAppId($miniProgram->getAppId());
            $alipayConfig->setPrivateKey($miniProgram->getPrivateKey());
            // 密钥模式
            $alipayConfig->setAlipayPublicKey($miniProgram->getAlipayPublicKey());
            // 证书模式
            // $alipayConfig->setAppCertPath('../appCertPublicKey.crt');
            // $alipayConfig->setAlipayPublicCertPath('../alipayCertPublicKey_RSA2.crt');
            // $alipayConfig->setRootCertPath('../alipayRootCert.crt');
            $alipayConfig->setEncryptKey($miniProgram->getEncryptKey());
            $alipayConfigUtil = new AlipayConfigUtil($alipayConfig);
            $apiInstance->setAlipayConfigUtil($alipayConfigUtil);

            $tokenModel = new AlipaySystemOauthTokenModel();
            $tokenModel->setGrantType('authorization_code');
            $tokenModel->setCode($this->authCode);

            try {
                $response = $apiInstance->token($tokenModel);
            } catch (\Throwable $exception) {
                throw new ApiException(sprintf('获取访问令牌失败：%s', $exception->getMessage()), previous: $exception);
            }

            // 查找或创建用户 todo 暂时使用userid,申请通过后替换成openid
            //            $user = $this->userRepository->findOneBy(['openId' => $response->getOpenId()]);
            //            if (!$user) {
            //                $user = new User();
            //                $user->setMiniProgram($miniProgram);
            //                $user->setOpenId($response->getOpenId());
            //            }
            $user = $this->userRepository->findOneBy(['openId' => $response->getUserId()]);
            if (!$user) {
                $user = new User();
                $user->setMiniProgram($miniProgram);
                $user->setOpenId($response->getUserId());
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // 如果是用户信息授权，获取用户信息
            if ($this->scope === AlipayAuthScope::AUTH_USER->value) {
                $message = new UpdateUserInfoMessage($user->getId(), $this->authCode);
                $this->messageBus->dispatch($message);
            }

            // 创建授权记录
            $authCodeEntity = new AuthCode();
            $authCodeEntity->setAlipayUser($user);
            $authCodeEntity->setAuthCode($this->authCode);
            $authCodeEntity->setScope(AlipayAuthScope::from($this->scope));
            $authCodeEntity->setOpenId($response->getUserId());
            $authCodeEntity->setUserId($response->getUserId());
            $authCodeEntity->setAccessToken($response->getAccessToken());
            $authCodeEntity->setRefreshToken($response->getRefreshToken());
            $authCodeEntity->setExpiresIn($response->getExpiresIn());
            $authCodeEntity->setReExpiresIn($response->getReExpiresIn());
            $authCodeEntity->setAuthStart(Carbon::createFromTimestamp($response->getAuthStart(), date_default_timezone_get()));
            $authCodeEntity->setCreatedFromIp($this->requestStack->getMainRequest()?->getClientIp());

            $this->entityManager->persist($user);
            $this->entityManager->persist($authCodeEntity);
            $this->entityManager->flush();

            // 获取或创建业务用户
            $bizUser = $this->userService->getBizUser($user);

            // 生成 JWT
            $token = $this->accessTokenService->createToken($bizUser);
        } catch (TimeoutException $exception) {
            throw new ApiException('支付宝接口超时，请稍后重试', 0, previous: $exception);
        } catch (UniqueConstraintViolationException $exception) {
            $this->getLockLogger()->error('授权失败:' . $exception->getMessage());
            throw new ApiException('请返回重试', previous: $exception);
        }

        $result = [
            'userId' => $user->getId(),
            'openId' => $user->getOpenId(),
            'accessToken' => $authCodeEntity->getAccessToken(),
            'refreshToken' => $authCodeEntity->getRefreshToken(),
            'expiresIn' => $authCodeEntity->getExpiresIn(),
            'reExpiresIn' => $authCodeEntity->getReExpiresIn(),
            'authStart' => $authCodeEntity->getAuthStart()->format('Y-m-d H:i:s'),
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
        return [
            'UploadAlipayMiniProgramAuthCode' . $params->get('authCode'),
        ];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): string
    {
        return 'UploadAlipayMiniProgramAuthCode-idempotent-' . $request->getParams()->get('authCode');
    }
}
