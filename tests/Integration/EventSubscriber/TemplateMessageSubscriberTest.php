<?php

namespace AlipayMiniProgramBundle\Tests\Integration\EventSubscriber;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\EventSubscriber\TemplateMessageSubscriber;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TemplateMessageSubscriberTest extends TestCase
{
    private TemplateMessageService|MockObject $templateMessageService;
    private TemplateMessageSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->templateMessageService = $this->createMock(TemplateMessageService::class);
        $this->subscriber = new TemplateMessageSubscriber($this->templateMessageService);
    }

    public function testPostPersist(): void
    {
        $message = $this->createMock(TemplateMessage::class);
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $args = new PostPersistEventArgs($message, $entityManager);
        
        $this->templateMessageService->expects($this->once())
            ->method('send')
            ->with($message);
        
        $this->subscriber->postPersist($message, $args);
    }
}