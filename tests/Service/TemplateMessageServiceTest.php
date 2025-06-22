<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TemplateMessageServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private TemplateMessageRepository|MockObject $templateMessageRepository;
    private TemplateMessageService $templateMessageService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->templateMessageRepository = $this->createMock(TemplateMessageRepository::class);
        $this->templateMessageService = new TemplateMessageService($this->entityManager, $this->templateMessageRepository);
    }

    public function testSend_whenAlreadySent(): void
    {
        // Arrange
        $message = $this->createMock(TemplateMessage::class);
        
        $message->expects($this->once())
            ->method('isSent')
            ->willReturn(true);
        
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // Act
        $result = $this->templateMessageService->send($message);
        
        // Assert
        $this->assertTrue($result);
    }
    
    public function testSend_whenException(): void
    {
        // Arrange
        $message = $this->createMock(TemplateMessage::class);
        $miniProgram = $this->createMock(MiniProgram::class);
        $exceptionMessage = 'Test exception';
        
        $message->expects($this->once())
            ->method('isSent')
            ->willReturn(false);
            
        $message->expects($this->once())
            ->method('getMiniProgram')
            ->willReturn($miniProgram);
            
        $miniProgram->expects($this->once())
            ->method('getAppId')
            ->willThrowException(new \Exception($exceptionMessage));
            
        $message->expects($this->once())
            ->method('setSendResult')
            ->with($exceptionMessage);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($message);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $result = $this->templateMessageService->send($message);
        
        // Assert
        $this->assertFalse($result);
    }
    
    public function testSendPendingMessages(): void
    {
        // Arrange
        $messages = [
            $this->createMock(TemplateMessage::class),
            $this->createMock(TemplateMessage::class),
        ];
        
        $this->templateMessageRepository->expects($this->once())
            ->method('findUnsentMessages')
            ->with(10)
            ->willReturn($messages);
            
        // Act
        $this->templateMessageService->sendPendingMessages();
    }
    
    public function testSendPendingMessages_withCustomLimit(): void
    {
        // Arrange
        $limit = 5;
        $messages = [];
        
        $this->templateMessageRepository->expects($this->once())
            ->method('findUnsentMessages')
            ->with($limit)
            ->willReturn($messages);
            
        // Act
        $this->templateMessageService->sendPendingMessages($limit);
    }
} 