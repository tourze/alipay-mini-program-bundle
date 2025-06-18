<?php

namespace AlipayMiniProgramBundle\Procedure;

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
#[MethodDoc('支付宝小程序上传手机号码')]
#[MethodTag('支付宝小程序')]
#[MethodExpose('UploadAlipayMiniProgramPhoneNumber')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class UploadAlipayMiniProgramPhoneNumber extends LockableProcedure
{
    /**
     * 加密数据
     */
    #[MethodParam('加密数据')]
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
        $user = $this->userService->requireAlipayUser($this->security->getUser());

        // 获取小程序配置
        $miniProgram = $user->getMiniProgram();
        $encryptedData = json_decode($this->encryptedData, true);

        try {
            if (!isset($encryptedData['response'])) {
                throw new ApiException('请返回重试');
            }
            // 解密手机号
            $encryptKey = $miniProgram->getEncryptKey();
            if ($encryptKey === null) {
                throw new ApiException('未配置加密密钥');
            }

            // 解密数据
            $decrypted = openssl_decrypt(
                base64_decode($encryptedData['response']),
                'AES-128-CBC',
                base64_decode($encryptKey),
                OPENSSL_RAW_DATA
            );

            if (false === $decrypted) {
                throw new ApiException('解密失败：' . openssl_error_string());
            }

            $phoneData = json_decode($decrypted, true);
            if ($phoneData === null || $phoneData === false) {
                throw new ApiException('解析数据失败：' . json_last_error_msg());
            }

            if (!isset($phoneData['mobile'])) {
                throw new ApiException('解密数据中未包含手机号');
            }

            // 绑定手机号
            $this->userService->bindPhone($user, $phoneData['mobile']);

            // 获取或创建业务用户
            $bizUser = $this->userService->getBizUser($user);

            // 更新业务用户手机号（仅当业务用户支持setMobile方法时）
            if (method_exists($bizUser, 'setMobile')) {
                $bizUser->setMobile($phoneData['mobile']);
                $this->entityManager->persist($bizUser);
                $this->entityManager->flush();
            }
        } catch (UniqueConstraintViolationException $exception) {
            throw new ApiException('请返回重试', previous: $exception);
        }

        return [
            'userId' => $user->getId(),
            'phoneNumber' => $phoneData['mobile'],
        ];
    }

    public function getLockResource(JsonRpcParams $params): ?array
    {
        return [
            'UploadAlipayMiniProgramPhoneNumber' . $params->get('userId'),
        ];
    }

    protected function getIdempotentCacheKey(JsonRpcRequest $request): string
    {
        return 'UploadAlipayMiniProgramPhoneNumber-idempotent-' . $request->getParams()->get('userId');
    }
}
