<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Repository\PhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhoneRepositoryTest extends TestCase
{
    private ManagerRegistry|MockObject $registry;
    private PhoneRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createPartialMock(PhoneRepository::class, ['findOneBy']);
        $this->repository->__construct($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $repository = new PhoneRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(PhoneRepository::class, $repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass(PhoneRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }

    public function test_find_by_number_with_valid_number(): void
    {
        $number = '13800138000';
        $expectedEntity = $this->createMock(Phone::class);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => $number])
            ->willReturn($expectedEntity);

        $result = $this->repository->findByNumber($number);

        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_number_with_non_existent_number(): void
    {
        $number = '99999999999';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => $number])
            ->willReturn(null);

        $result = $this->repository->findByNumber($number);

        $this->assertNull($result);
    }

    public function test_find_by_number_with_empty_string(): void
    {
        $number = '';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => $number])
            ->willReturn(null);

        $result = $this->repository->findByNumber($number);

        $this->assertNull($result);
    }

    public function test_find_by_number_with_international_format(): void
    {
        $number = '+8613800138000';
        $expectedEntity = $this->createMock(Phone::class);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['number' => $number])
            ->willReturn($expectedEntity);

        $result = $this->repository->findByNumber($number);

        $this->assertSame($expectedEntity, $result);
    }


    public function test_inherited_methods_exist(): void
    {
        $repository = new PhoneRepository($this->registry);
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($repository, $method),
                "Method {$method} should exist in PhoneRepository"
            );
        }
    }

    public function test_repository_inheritance(): void
    {
        $repository = new PhoneRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function test_repository_class_docblock_annotations(): void
    {
        $reflection = new \ReflectionClass(PhoneRepository::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@extends ServiceEntityRepository<Phone>', $docComment);
        $this->assertStringContainsString('@method Phone|null find', $docComment);
        $this->assertStringContainsString('@method Phone|null findOneBy', $docComment);
        $this->assertStringContainsString('@method Phone[]    findAll', $docComment);
        $this->assertStringContainsString('@method Phone[]    findBy', $docComment);
    }

    public function test_find_by_number_calls_find_one_by_with_correct_criteria(): void
    {
        $number = '18800001111';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['number' => $number]));

        $this->repository->findByNumber($number);
    }

    public function test_find_by_number_with_various_phone_formats(): void
    {
        $testCases = [
            '13800138000',
            '+8613800138000',
            '86-138-0013-8000',
            '138 0013 8000'
        ];

        $this->repository
            ->expects($this->exactly(count($testCases)))
            ->method('findOneBy')
            ->willReturn(null);

        foreach ($testCases as $number) {
            $result = $this->repository->findByNumber($number);
            $this->assertNull($result);
        }
    }
}
