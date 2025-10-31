<?php

namespace AlipayMiniProgramBundle\Procedure;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
 * @see https://opendocs.alipay.com/mini/api/getphonenumber
 */
#[MethodDoc(summary: '支付宝小程序上传手机号码')]
#[MethodTag(name: '支付宝小程序')]
#[MethodExpose(method: 'UploadAlipayMiniProgramPhoneNumber')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class UploadAlipayMiniProgramPhoneNumber extends LockableProcedure
{
    /**
     * 加密数据
     */
    #[MethodParam(description: '加密数据')]
    #[Assert\NotNull]
    public string $encryptedData;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        // 获取用户
        $bizUser = $this->security->getUser();
        if (null === $bizUser) {
            throw new ApiException('用户未登录');
        }

        $user = $this->userService->requireAlipayUser($bizUser);

        try {
            $mobile = $this->decryptPhoneNumber($user->getMiniProgram());
            $this->userService->bindPhone($user, $mobile);
            $this->updateBizUserMobile($user, $mobile);
        } catch (UniqueConstraintViolationException $exception) {
            throw new ApiException('请返回重试', previous: $exception);
        }

        return [
            'userId' => $user->getId(),
            'phoneNumber' => $mobile,
        ];
    }

    private function decryptPhoneNumber(MiniProgram $miniProgram): string
    {
        $encryptedData = json_decode($this->encryptedData, true);
        if (!is_array($encryptedData)) {
            throw new ApiException('无效的加密数据格式');
        }

        $response = $encryptedData['response'] ?? null;
        if (!is_string($response)) {
            throw new ApiException('请返回重试');
        }

        return $this->decryptResponse($response, $miniProgram->getEncryptKey());
    }

    private function decryptResponse(string $response, ?string $encryptKey): string
    {
        if (null === $encryptKey) {
            throw new ApiException('未配置加密密钥');
        }

        // 解密数据
        $decodedResponse = base64_decode($response, true);
        if (false === $decodedResponse) {
            throw new ApiException('响应数据Base64解码失败');
        }

        $decodedKey = base64_decode($encryptKey, true);
        if (false === $decodedKey) {
            throw new ApiException('加密密钥Base64解码失败');
        }

        $decrypted = openssl_decrypt(
            $decodedResponse,
            'AES-128-CBC',
            $decodedKey,
            OPENSSL_RAW_DATA
        );

        if (false === $decrypted) {
            throw new ApiException('解密失败：' . openssl_error_string());
        }

        $phoneData = json_decode($decrypted, true);
        if (!is_array($phoneData)) {
            throw new ApiException('解析数据失败：' . json_last_error_msg());
        }

        $mobile = $phoneData['mobile'] ?? null;
        if (!is_string($mobile)) {
            throw new ApiException('解密数据中未包含手机号');
        }

        return $mobile;
    }

    private function updateBizUserMobile(User $user, string $mobile): void
    {
        // 获取或创建业务用户
        $bizUser = $this->userService->getBizUser($user);

        // 更新业务用户手机号（仅当业务用户支持setMobile方法时）
        if (method_exists($bizUser, 'setMobile')) {
            $bizUser->setMobile($mobile);
            $this->entityManager->persist($bizUser);
            $this->entityManager->flush();
        }
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        $userId = $params->get('userId');

        return [
            'UploadAlipayMiniProgramPhoneNumber' . (is_string($userId) || is_numeric($userId) ? $userId : ''),
        ];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            throw new ApiException('请求参数不能为空');
        }

        $userId = $params->get('userId');

        return 'UploadAlipayMiniProgramPhoneNumber-idempotent-' . (is_string($userId) || is_numeric($userId) ? $userId : '');
    }
}
