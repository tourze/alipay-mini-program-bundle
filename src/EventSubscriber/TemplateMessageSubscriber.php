<?php

namespace AlipayMiniProgramBundle\EventSubscriber;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TemplateMessage::class)]
class TemplateMessageSubscriber
{
    public function __construct(
        private readonly TemplateMessageService $templateMessageService,
    ) {
    }

    public function postPersist(TemplateMessage $message, PostPersistEventArgs $args): void
    {
        $this->templateMessageService->send($message);
    }
}
