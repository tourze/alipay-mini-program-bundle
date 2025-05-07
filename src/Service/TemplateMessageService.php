<?php

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use Doctrine\ORM\EntityManagerInterface;

class TemplateMessageService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SdkService $sdkService,
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

        try {
            $config = $this->sdkService->getSdkFromMiniProgram($message->getMiniProgram());

            // 构造请求参数
            $bizContent = [
                'to_open_id' => $message->getToOpenId(),
                'user_template_id' => $message->getTemplateId(),
                'page' => $message->getPage(),
                'data' => $message->getData(),
            ];

            // 发送请求
            $client = new \AopClient($config);
            $request = new \AlipayOpenAppMiniTemplatemessageSendRequest();
            $request->setBizContent(json_encode($bizContent, JSON_UNESCAPED_UNICODE));

            $result = $client->execute($request);
            $response = $result->alipay_open_app_mini_templatemessage_send_response;

            $message->setIsSent(true);
            $message->setSentTime(new \DateTimeImmutable());
            $message->setSendResult('10000' === $response->code ? 'success' : ($response->sub_msg ?? $response->msg ?? 'unknown error'));

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return '10000' === $response->code;
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
        $messages = $this->entityManager->getRepository(TemplateMessage::class)
            ->findUnsentMessages($limit);

        foreach ($messages as $message) {
            $this->send($message);
        }
    }
}
