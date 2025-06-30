<?php

namespace AlipayMiniProgramBundle\Service;

use Alipay\OpenAPISDK\Api\AlipayOpenAppMiniTemplatemessageApi;
use Alipay\OpenAPISDK\Model\AlipayOpenAppMiniTemplatemessageSendModel;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Exception\InvalidMiniProgramConfigurationException;
use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class TemplateMessageService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TemplateMessageRepository $templateMessageRepository,
    ) {}

    /**
     * 发送模板消息
     */
    public function send(TemplateMessage $message): bool
    {
        if ($message->isSent()) {
            return true;
        }

        try {
            $apiInstance = new AlipayOpenAppMiniTemplatemessageApi(
                // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                // This is optional, `GuzzleHttp\Client` will be used as default.
                new \GuzzleHttp\Client()
            );

            // 初始化alipay参数
            $miniProgram = $message->getMiniProgram();
            
            // 检查必需的配置字段
            $appId = $miniProgram->getAppId();
            $privateKey = $miniProgram->getPrivateKey();
            $alipayPublicKey = $miniProgram->getAlipayPublicKey();
            
            if ($appId === null || $privateKey === null || $alipayPublicKey === null) {
                throw new InvalidMiniProgramConfigurationException('MiniProgram configuration is incomplete');
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
            if ($encryptKey !== null) {
                $alipayConfig->setEncryptKey($encryptKey);
            }
            $alipayConfigUtil = new \Alipay\OpenAPISDK\Util\AlipayConfigUtil($alipayConfig);
            $apiInstance->setAlipayConfigUtil($alipayConfigUtil);

            $sendModel = new AlipayOpenAppMiniTemplatemessageSendModel();
            $sendModel->setToOpenId($message->getToOpenId());
            $sendModel->setUserTemplateId($message->getTemplateId());
            $sendModel->setPage($message->getPage());
            $sendModel->setData(json_encode($message->getData()));

            $response = $apiInstance->send($sendModel);

            $message->setIsSent(true);
            $message->setSentTime(new \DateTimeImmutable());
            $message->setSendResult('10000' === $response->getCode() ? 'success' : ($response->getMessage() ?? 'unknown error'));

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $message->setSendResult($e->getMessage());
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return false;
        }
    }

    /**
     * 批量发送未发送的消息
     */
    public function sendPendingMessages(int $limit = 10): void
    {
        $messages = $this->templateMessageRepository->findUnsentMessages($limit);

        foreach ($messages as $message) {
            $this->send($message);
        }
    }
}
