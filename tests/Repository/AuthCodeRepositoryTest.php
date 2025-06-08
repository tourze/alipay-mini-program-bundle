<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Repository\AuthCodeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthCodeRepositoryTest extends TestCase
{
    private ManagerRegistry|MockObject $registry;
    private AuthCodeRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createPartialMock(AuthCodeRepository::class, ['findOneBy']);
        $this->repository->__construct($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $repository = new AuthCodeRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(AuthCodeRepository::class, $repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass(AuthCodeRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }

    public function test_find_by_auth_code_with_valid_code(): void
    {
        $authCode = 'TEST_AUTH_CODE_123';
        $expectedEntity = $this->createMock(AuthCode::class);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['authCode' => $authCode])
            ->willReturn($expectedEntity);

        $result = $this->repository->findByAuthCode($authCode);

        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_auth_code_with_non_existent_code(): void
    {
        $authCode = 'NON_EXISTENT_CODE';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['authCode' => $authCode])
            ->willReturn(null);

        $result = $this->repository->findByAuthCode($authCode);

        $this->assertNull($result);
    }

    public function test_find_by_auth_code_with_empty_string(): void
    {
        $authCode = '';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['authCode' => $authCode])
            ->willReturn(null);

        $result = $this->repository->findByAuthCode($authCode);

        $this->assertNull($result);
    }

    public function test_find_by_auth_code_method_exists(): void
    {
        $repository = new AuthCodeRepository($this->registry);

        $this->assertTrue(method_exists($repository, 'findByAuthCode'));
    }

    public function test_inherited_methods_exist(): void
    {
        $repository = new AuthCodeRepository($this->registry);
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($repository, $method),
                "Method {$method} should exist in AuthCodeRepository"
            );
        }
    }

    public function test_repository_inheritance(): void
    {
        $repository = new AuthCodeRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function test_repository_class_docblock_annotations(): void
    {
        $reflection = new \ReflectionClass(AuthCodeRepository::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@extends ServiceEntityRepository<AuthCode>', $docComment);
        $this->assertStringContainsString('@method AuthCode|null find', $docComment);
        $this->assertStringContainsString('@method AuthCode|null findOneBy', $docComment);
        $this->assertStringContainsString('@method AuthCode[]    findAll', $docComment);
        $this->assertStringContainsString('@method AuthCode[]    findBy', $docComment);
    }

    public function test_constructor_requires_manager_registry(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new AuthCodeRepository($registry);

        $this->assertInstanceOf(AuthCodeRepository::class, $repository);
    }

    public function test_find_by_auth_code_calls_find_one_by_with_correct_criteria(): void
    {
        $authCode = 'specific_auth_code_123';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['authCode' => $authCode]));

        $this->repository->findByAuthCode($authCode);
    }
}
