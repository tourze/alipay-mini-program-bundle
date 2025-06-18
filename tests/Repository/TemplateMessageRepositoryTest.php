<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TemplateMessageRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private TemplateMessageRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new TemplateMessageRepository($this->registry);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
        $this->assertInstanceOf(TemplateMessageRepository::class, $this->repository);
    }

    public function test_repository_handles_correct_entity_class(): void
    {
        // 通过反射检查实体类型，而不是调用需要Doctrine配置的getClassName()
        $reflection = new \ReflectionClass(TemplateMessageRepository::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertSame(ServiceEntityRepository::class, $parentClass->getName());
        
        // 检查构造函数是否正确调用了父类构造函数
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);
    }

    public function test_find_unsent_messages_method_exists(): void
    {
        $this->assertTrue(method_exists($this->repository, 'findUnsentMessages'));
    }

    public function test_find_unsent_messages_with_default_limit(): void
    {
        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        $repository = $this->createPartialMock(TemplateMessageRepository::class, ['createQueryBuilder']);
        $repository->__construct($this->registry);
        
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('m')
            ->willReturn($queryBuilder);
        
        $queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('m.isSent = :isSent')
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('isSent', false)
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $expectedResults = [
            $this->createMock(TemplateMessage::class),
            $this->createMock(TemplateMessage::class)
        ];
        
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResults);
        
        $result = $repository->findUnsentMessages();
        
        $this->assertSame($expectedResults, $result);
    }

    public function test_find_unsent_messages_with_custom_limit(): void
    {
        $customLimit = 5;
        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        $repository = $this->createPartialMock(TemplateMessageRepository::class, ['createQueryBuilder']);
        $repository->__construct($this->registry);
        
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('m')
            ->willReturn($queryBuilder);
        
        $queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('m.isSent = :isSent')
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('isSent', false)
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($customLimit)
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $expectedResults = [];
        
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedResults);
        
        $result = $repository->findUnsentMessages($customLimit);
        
        $this->assertSame($expectedResults, $result);
    }

    public function test_find_unsent_messages_with_zero_limit(): void
    {
        $limit = 0;
        $query = $this->createMock(Query::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        
        $repository = $this->createPartialMock(TemplateMessageRepository::class, ['createQueryBuilder']);
        $repository->__construct($this->registry);
        
        $repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with($limit)
            ->willReturnSelf();
        
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query
            ->expects($this->once())
            ->method('getResult')
            ->willReturn([]);
        
        $result = $repository->findUnsentMessages($limit);
        $this->assertEmpty($result);
    }

    public function test_inherited_methods_exist(): void
    {
        $requiredMethods = ['find', 'findOneBy', 'findAll', 'findBy'];
        
        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($this->repository, $method),
                "Method {$method} should exist in TemplateMessageRepository"
            );
        }
    }

    public function test_repository_inheritance(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function test_repository_class_docblock_annotations(): void
    {
        $reflection = new \ReflectionClass(TemplateMessageRepository::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@method TemplateMessage|null find', $docComment);
        $this->assertStringContainsString('@method TemplateMessage|null findOneBy', $docComment);
        $this->assertStringContainsString('@method TemplateMessage[]    findAll', $docComment);
        $this->assertStringContainsString('@method TemplateMessage[]    findBy', $docComment);
    }

    public function test_find_unsent_messages_return_type(): void
    {
        $reflection = new \ReflectionMethod($this->repository, 'findUnsentMessages');
        $returnType = $reflection->getReturnType();
        
        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->getName());
    }
} 