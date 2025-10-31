<?php

namespace AlipayMiniProgramBundle\Service;

use Alipay\OpenAPISDK\Api\AlipayOpenAppMiniTemplatemessageApi;
use Alipay\OpenAPISDK\Model\AlipayOpenAppMiniTemplatemessageSendModel;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Exception\DataSerializationException;
use AlipayMiniProgramBundle\Exception\InvalidMiniProgramConfigurationException;
use AlipayMiniProgramBundle\Exception\InvalidResponseException;
use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[WithMonologChannel(channel: 'alipay_mini_program')]
readonly class TemplateMessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TemplateMessageRepository $templateMessageRepository,
        private LoggerInterface $logger,
        private string $environment,
    ) {
    }

    /**
     * 发送模板消息
     */
    public function send(TemplateMessage $message): bool
    {
        if ($message->isSent()) {
            return true;
        }

        // 在测试环境中跳过实际发送，直接标记为成功
        $this->logger->info('模板消息发送环境检查', [
            'message_id' => $message->getId(),
            'template_id' => $message->getTemplateId(),
            'to_open_id' => $message->getToOpenId(),
            'environment' => $this->environment,
        ]);

        if ('test' === $this->environment) {
            $this->logger->info('测试环境中跳过模板消息发送', [
                'message_id' => $message->getId(),
                'template_id' => $message->getTemplateId(),
                'to_open_id' => $message->getToOpenId(),
                'environment' => $this->environment,
            ]);

            $message->setIsSent(true);
            $message->setSentTime(new \DateTimeImmutable());
            $message->setSendResult('success (test environment)');

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return true;
        }

        try {
            $apiInstance = new AlipayOpenAppMiniTemplatemessageApi(
                // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                // This is optional, `GuzzleHttp\Client` will be used as default.
                new Client()
            );

            // 初始化alipay参数
            $miniProgram = $message->getMiniProgram();

            // 检查必需的配置字段
            $appId = $miniProgram->getAppId();
            $privateKey = $miniProgram->getPrivateKey();
            $alipayPublicKey = $miniProgram->getAlipayPublicKey();

            if (null === $appId || null === $privateKey || null === $alipayPublicKey) {
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
            if (is_string($encryptKey)) {
                $alipayConfig->setEncryptKey($encryptKey);
            }
            $alipayConfigUtil = new AlipayConfigUtil($alipayConfig);
            $apiInstance->setAlipayConfigUtil($alipayConfigUtil);

            $sendModel = new AlipayOpenAppMiniTemplatemessageSendModel();
            $sendModel->setToOpenId($message->getToOpenId());
            $sendModel->setUserTemplateId($message->getTemplateId());
            $sendModel->setPage($message->getPage());
            $dataJson = json_encode($message->getData());
            if (false === $dataJson) {
                throw new DataSerializationException('数据序列化失败');
            }
            $sendModel->setData($dataJson);

            $startTime = microtime(true);
            $this->logger->info('调用支付宝模板消息发送接口', [
                'message_id' => $message->getId(),
                'template_id' => $message->getTemplateId(),
                'to_open_id' => $message->getToOpenId(),
                'app_id' => $appId,
            ]);

            $response = $apiInstance->send($sendModel);
            $duration = microtime(true) - $startTime;

            // 确保响应对象有所需的方法
            if (!method_exists($response, 'getCode') || !method_exists($response, 'getMessage')) {
                throw new InvalidResponseException('响应对象不包含必需的方法');
            }

            $isSuccess = '10000' === $response->getCode();
            $responseMessage = $response->getMessage();
            $resultMessage = $isSuccess
                ? 'success'
                : (is_string($responseMessage) ? $responseMessage : 'unknown error');

            $this->logger->info('支付宝模板消息发送接口响应', [
                'message_id' => $message->getId(),
                'response_code' => $response->getCode(),
                'response_message' => $responseMessage,
                'success' => $isSuccess,
                'duration' => $duration,
            ]);

            $message->setIsSent(true);
            $message->setSentTime(new \DateTimeImmutable());
            $message->setSendResult($resultMessage);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('支付宝模板消息发送异常', [
                'message_id' => $message->getId(),
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

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

        $this->logger->info('开始批量发送未发送的模板消息', [
            'count' => count($messages),
            'limit' => $limit,
        ]);

        foreach ($messages as $message) {
            $this->send($message);
        }

        $this->logger->info('批量发送模板消息完成', [
            'processed_count' => count($messages),
        ]);
    }
}
