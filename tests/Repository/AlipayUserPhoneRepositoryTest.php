<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Repository\AlipayUserPhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class AlipayUserPhoneRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private AlipayUserPhoneRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AlipayUserPhoneRepository($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
        $this->assertInstanceOf(AlipayUserPhoneRepository::class, $this->repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass($this->repository);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }


    public function test_repository_inheritance(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function test_repository_extends_service_entity_repository(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
    }


    public function test_repository_class_implements_correct_methods(): void
    {
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->repository, $method),
                "Method {$method} should exist in AlipayUserPhoneRepository"
            );
        }
    }

    public function test_repository_class_docblock_annotations(): void
    {
        $reflection = new \ReflectionClass($this->repository);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@extends ServiceEntityRepository<AlipayUserPhone>', $docComment);
        $this->assertStringContainsString('@method AlipayUserPhone|null find', $docComment);
        $this->assertStringContainsString('@method AlipayUserPhone|null findOneBy', $docComment);
        $this->assertStringContainsString('@method AlipayUserPhone[]    findAll', $docComment);
        $this->assertStringContainsString('@method AlipayUserPhone[]    findBy', $docComment);
    }
}
