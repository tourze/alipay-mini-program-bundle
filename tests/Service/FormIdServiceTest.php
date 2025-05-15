<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\FormIdRepository;
use AlipayMiniProgramBundle\Service\FormIdService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormIdServiceTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private FormIdRepository|MockObject $formIdRepository;
    private FormIdService $formIdService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->formIdRepository = $this->createMock(FormIdRepository::class);
        
        $this->formIdService = new FormIdService(
            $this->entityManager,
            $this->formIdRepository
        );
    }

    public function testSaveFormId_withValidData(): void
    {
        // Arrange
        $miniProgram = $this->createMock(MiniProgram::class);
        $user = $this->createMock(User::class);
        $formId = 'test_form_id_123';
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($entity) use ($miniProgram, $user, $formId) {
                return $entity instanceof FormId
                    && $entity->getMiniProgram() === $miniProgram
                    && $entity->getUser() === $user
                    && $entity->getFormId() === $formId
                    && $entity->getUsedCount() === 0
                    && $entity->getExpireTime() instanceof \DateTimeInterface;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $result = $this->formIdService->saveFormId($miniProgram, $user, $formId);
        
        // Assert
        $this->assertInstanceOf(FormId::class, $result);
        $this->assertSame($miniProgram, $result->getMiniProgram());
        $this->assertSame($user, $result->getUser());
        $this->assertSame($formId, $result->getFormId());
        $this->assertEquals(0, $result->getUsedCount());
        $this->assertGreaterThan(new \DateTime(), $result->getExpireTime());
    }

    public function testGetAvailableFormId_whenFormIdExists(): void
    {
        // Arrange
        $miniProgram = $this->createMock(MiniProgram::class);
        $user = $this->createMock(User::class);
        $formId = new FormId();
        $formId->setUsedCount(1);
        
        $this->formIdRepository->expects($this->once())
            ->method('findAvailableFormId')
            ->with($miniProgram, $user)
            ->willReturn($formId);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // Act
        $result = $this->formIdService->getAvailableFormId($miniProgram, $user);
        
        // Assert
        $this->assertSame($formId, $result);
        $this->assertEquals(2, $result->getUsedCount()); // Should be incremented
    }
    
    public function testGetAvailableFormId_whenNoFormIdExists(): void
    {
        // Arrange
        $miniProgram = $this->createMock(MiniProgram::class);
        $user = $this->createMock(User::class);
        
        $this->formIdRepository->expects($this->once())
            ->method('findAvailableFormId')
            ->with($miniProgram, $user)
            ->willReturn(null);
            
        $this->entityManager->expects($this->never())
            ->method('flush');
        
        // Act
        $result = $this->formIdService->getAvailableFormId($miniProgram, $user);
        
        // Assert
        $this->assertNull($result);
    }
    
    public function testCleanExpiredFormIds(): void
    {
        // Arrange
        $deletedCount = 5;
        
        $this->formIdRepository->expects($this->once())
            ->method('cleanExpiredFormIds')
            ->willReturn($deletedCount);
        
        // Act
        $result = $this->formIdService->cleanExpiredFormIds();
        
        // Assert
        $this->assertEquals($deletedCount, $result);
    }
} 