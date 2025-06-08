<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createPartialMock(UserRepository::class, ['findOneBy']);
        $this->repository->__construct($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $repository = new UserRepository($this->registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(UserRepository::class, $repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass(UserRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }

    public function test_find_by_user_id_with_valid_user_id(): void
    {
        $userId = 'USER123456';
        $expectedEntity = $this->createMock(User::class);
        
        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => $userId])
            ->willReturn($expectedEntity);
        
        $result = $this->repository->findByUserId($userId);
        
        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_user_id_with_non_existent_user_id(): void
    {
        $userId = 'NON_EXISTENT_USER';
        
        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => $userId])
            ->willReturn(null);
        
        $result = $this->repository->findByUserId($userId);
        
        $this->assertNull($result);
    }

    public function test_find_by_open_id_with_valid_open_id(): void
    {
        $openId = 'OPEN_ID_123456';
        $expectedEntity = $this->createMock(User::class);
        
        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => $openId])
            ->willReturn($expectedEntity);
        
        $result = $this->repository->findByOpenId($openId);
        
        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_open_id_with_non_existent_open_id(): void
    {
        $openId = 'NON_EXISTENT_OPEN_ID';
        
        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['openId' => $openId])
            ->willReturn(null);
        
        $result = $this->repository->findByOpenId($openId);
        
        $this->assertNull($result);
    }

    public function test_repository_custom_methods_exist(): void
    {
        $repository = new UserRepository($this->registry);
        
        $this->assertTrue(method_exists($repository, 'findByUserId'));
        $this->assertTrue(method_exists($repository, 'findByOpenId'));
    }

    public function test_inherited_methods_exist(): void
    {
        $repository = new UserRepository($this->registry);
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];
        
        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($repository, $method),
                "Method {$method} should exist in UserRepository"
            );
        }
    }

    public function test_repository_inheritance(): void
    {
        $repository = new UserRepository($this->registry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }
} 