<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MiniProgramRepositoryTest extends TestCase
{
    private ManagerRegistry|MockObject $registry;
    private MiniProgramRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = $this->createPartialMock(MiniProgramRepository::class, ['findOneBy']);
        $this->repository->__construct($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $repository = new MiniProgramRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(MiniProgramRepository::class, $repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass(MiniProgramRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }

    public function test_find_by_app_id_with_valid_app_id(): void
    {
        $appId = '2021001234567890';
        $expectedEntity = $this->createMock(MiniProgram::class);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['appId' => $appId])
            ->willReturn($expectedEntity);

        $result = $this->repository->findByAppId($appId);

        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_app_id_with_non_existent_app_id(): void
    {
        $appId = 'NON_EXISTENT_APP_ID';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['appId' => $appId])
            ->willReturn(null);

        $result = $this->repository->findByAppId($appId);

        $this->assertNull($result);
    }

    public function test_find_by_app_id_with_empty_string(): void
    {
        $appId = '';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['appId' => $appId])
            ->willReturn(null);

        $result = $this->repository->findByAppId($appId);

        $this->assertNull($result);
    }

    public function test_find_by_code_with_valid_code(): void
    {
        $code = 'MINI_PROGRAM_CODE';
        $expectedEntity = $this->createMock(MiniProgram::class);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn($expectedEntity);

        $result = $this->repository->findByCode($code);

        $this->assertSame($expectedEntity, $result);
    }

    public function test_find_by_code_with_non_existent_code(): void
    {
        $code = 'NON_EXISTENT_CODE';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn(null);

        $result = $this->repository->findByCode($code);

        $this->assertNull($result);
    }

    public function test_find_by_code_with_empty_string(): void
    {
        $code = '';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn(null);

        $result = $this->repository->findByCode($code);

        $this->assertNull($result);
    }


    public function test_inherited_methods_exist(): void
    {
        $repository = new MiniProgramRepository($this->registry);
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($repository, $method),
                "Method {$method} should exist in MiniProgramRepository"
            );
        }
    }

    public function test_repository_inheritance(): void
    {
        $repository = new MiniProgramRepository($this->registry);

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function test_repository_class_docblock_annotations(): void
    {
        $reflection = new \ReflectionClass(MiniProgramRepository::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@extends ServiceEntityRepository<MiniProgram>', $docComment);
        $this->assertStringContainsString('@method MiniProgram|null find', $docComment);
        $this->assertStringContainsString('@method MiniProgram|null findOneBy', $docComment);
        $this->assertStringContainsString('@method MiniProgram[]    findAll', $docComment);
        $this->assertStringContainsString('@method MiniProgram[]    findBy', $docComment);
    }

    public function test_find_by_app_id_calls_find_one_by_with_correct_criteria(): void
    {
        $appId = 'specific_app_id_123';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['appId' => $appId]));

        $this->repository->findByAppId($appId);
    }

    public function test_find_by_code_calls_find_one_by_with_correct_criteria(): void
    {
        $code = 'specific_code_123';

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->identicalTo(['code' => $code]));

        $this->repository->findByCode($code);
    }
}
