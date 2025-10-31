<?php

namespace AlipayMiniProgramBundle\EventSubscriber;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TemplateMessage::class)]
#[WithMonologChannel(channel: 'alipay_mini_program')]
readonly class TemplateMessageSubscriber
{
    public function __construct(
        private TemplateMessageService $templateMessageService,
        private LoggerInterface $logger,
        #[Autowire(param: '%kernel.environment%')] private string $environment,
    ) {
    }

    public function postPersist(TemplateMessage $message, PostPersistEventArgs $args): void
    {
        // 在测试环境中跳过实际发送
        if ('test' === $this->environment) {
            $this->logger->info('测试环境中跳过模板消息发送', [
                'message_id' => $message->getId(),
                'template_id' => $message->getTemplateId(),
                'to_open_id' => $message->getToOpenId(),
                'environment' => $this->environment,
            ]);

            return;
        }

        $startTime = microtime(true);
        $this->logger->info('开始发送模板消息', [
            'message_id' => $message->getId(),
            'template_id' => $message->getTemplateId(),
            'to_open_id' => $message->getToOpenId(),
        ]);

        try {
            $result = $this->templateMessageService->send($message);
            $duration = microtime(true) - $startTime;

            $this->logger->info('模板消息发送完成', [
                'message_id' => $message->getId(),
                'result' => $result ? 'success' : 'failed',
                'duration' => $duration,
                'send_result' => $message->getSendResult(),
            ]);
        } catch (\Throwable $exception) {
            $duration = microtime(true) - $startTime;
            $this->logger->error('模板消息发送异常', [
                'message_id' => $message->getId(),
                'error' => $exception->getMessage(),
                'duration' => $duration,
                'exception' => get_class($exception),
            ]);
            throw $exception;
        }
    }
}
