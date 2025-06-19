<?php

namespace AlipayMiniProgramBundle\MessageHandler;

use Alipay\OpenAPISDK\Api\AlipayUserInfoApi;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserInfoHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function __invoke(UpdateUserInfoMessage $message): void
    {
        $user = $this->userRepository->find($message->getUserId());
        if ($user === null) {
            return;
        }

        $apiInstance = new AlipayUserInfoApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client()
        );

        // 初始化alipay参数
        $alipayConfig = new AlipayConfig();
        $alipayConfig->setAppId($user->getMiniProgram()->getAppId());
        $alipayConfig->setPrivateKey($user->getMiniProgram()->getPrivateKey());
        // 密钥模式
        $alipayConfig->setAlipayPublicKey($user->getMiniProgram()->getAlipayPublicKey());
        // 证书模式
        // $alipayConfig->setAppCertPath('../appCertPublicKey.crt');
        // $alipayConfig->setAlipayPublicCertPath('../alipayCertPublicKey_RSA2.crt');
        // $alipayConfig->setRootCertPath('../alipayRootCert.crt');
        $alipayConfig->setEncryptKey($user->getMiniProgram()->getEncryptKey());
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

        // 更新用户信息
        $user->setNickName($userInfoResponse->getNickName());
        $user->setAvatar($userInfoResponse->getAvatar());
        $user->setProvince($userInfoResponse->getProvince());
        $user->setCity($userInfoResponse->getCity());
        $gender = $userInfoResponse->getGender();
        if ($gender !== null) {
            $user->setGender(AlipayUserGender::from($gender));
        }
        $user->setLastInfoUpdateTime(CarbonImmutable::now()->toDateTimeImmutable());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
