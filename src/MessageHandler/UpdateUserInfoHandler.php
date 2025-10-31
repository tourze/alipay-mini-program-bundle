<?php

namespace AlipayMiniProgramBundle\MessageHandler;

use Alipay\OpenAPISDK\Api\AlipayUserInfoApi;
use Alipay\OpenAPISDK\Model\AlipayUserInfoShareResponseModel;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
#[WithMonologChannel(channel: 'alipay_mini_program')]
readonly class UpdateUserInfoHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UpdateUserInfoMessage $message): void
    {
        $user = $this->userRepository->find($message->getUserId());
        if (null === $user) {
            return;
        }

        $apiInstance = new AlipayUserInfoApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new Client()
        );

        // 初始化alipay参数
        $miniProgram = $user->getMiniProgram();

        // 检查必需的配置字段
        $appId = $miniProgram->getAppId();
        $privateKey = $miniProgram->getPrivateKey();
        $alipayPublicKey = $miniProgram->getAlipayPublicKey();

        if (null === $appId || null === $privateKey || null === $alipayPublicKey) {
            $this->logger->error('MiniProgram configuration is incomplete', [
                'userId' => $user->getId(),
                'miniProgramId' => $miniProgram->getId(),
                'hasAppId' => null !== $appId,
                'hasPrivateKey' => null !== $privateKey,
                'hasAlipayPublicKey' => null !== $alipayPublicKey,
            ]);

            return;
        }

        $alipayConfig = new AlipayConfig();
        $alipayConfig->setAppId($appId);
        $alipayConfig->setPrivateKey($privateKey);
        // 密钥模式
        $alipayConfig->setAlipayPublicKey($alipayPublicKey);
        // 证书模式
        // $alipayConfig->setAppCertPath('../appCertPublicKey.crt');
        // $alipayConfig->setAlipayPublicCertPath('../alipayCertPublicKey_RSA2.crt');
        // $alipayConfig->setRootCertPath('../alipayRootCert.crt');
        $encryptKey = $miniProgram->getEncryptKey();
        if (is_string($encryptKey)) {
            $alipayConfig->setEncryptKey($encryptKey);
        }
        $alipayConfigUtil = new AlipayConfigUtil($alipayConfig);
        $apiInstance->setAlipayConfigUtil($alipayConfigUtil);

        try {
            $userInfoResponse = $apiInstance->share($message->getAuthToken());
        } catch (\Throwable $e) {
            $this->logger->error('Exception when calling AlipayUserInfoApi->share', [
                'exception' => $e,
                'authToken' => $message->getAuthToken(),
                'userId' => $user->getId(),
            ]);

            return;
        }

        // 检查响应类型并更新用户信息
        if (!$userInfoResponse instanceof AlipayUserInfoShareResponseModel) {
            $this->logger->error('Invalid user info response type', [
                'response_type' => get_class($userInfoResponse),
                'userId' => $user->getId(),
            ]);

            return;
        }

        // 更新用户信息
        $user->setNickName($userInfoResponse->getNickName());
        $user->setAvatar($userInfoResponse->getAvatar());
        $user->setProvince($userInfoResponse->getProvince());
        $user->setCity($userInfoResponse->getCity());
        $gender = $userInfoResponse->getGender();
        if (null !== $gender) {
            $user->setGender(AlipayUserGender::from($gender));
        }
        $user->setLastInfoUpdateTime(CarbonImmutable::now()->toDateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
