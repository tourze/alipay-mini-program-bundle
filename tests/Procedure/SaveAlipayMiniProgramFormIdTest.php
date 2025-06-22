<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Procedure\SaveAlipayMiniProgramFormId;
use AlipayMiniProgramBundle\Service\FormIdService;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

class SaveAlipayMiniProgramFormIdTest extends TestCase
{
    private FormIdService|MockObject $formIdService;
    private EntityManagerInterface|MockObject $entityManager;
    private UserService|MockObject $userService;
    private Security|MockObject $security;
    private SaveAlipayMiniProgramFormId $procedure;

    protected function setUp(): void
    {
        $this->formIdService = $this->createMock(FormIdService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userService = $this->createMock(UserService::class);
        $this->security = $this->createMock(Security::class);
        
        $this->procedure = new SaveAlipayMiniProgramFormId(
            $this->formIdService,
            $this->entityManager,
            $this->userService,
            $this->security
        );
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(LockableProcedure::class, $this->procedure);
        $this->assertInstanceOf(SaveAlipayMiniProgramFormId::class, $this->procedure);
    }

    public function test_procedure_has_required_properties(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        
        $this->assertTrue($reflection->hasProperty('miniProgramId'));
        $this->assertTrue($reflection->hasProperty('formId'));
        
        $miniProgramIdProperty = $reflection->getProperty('miniProgramId');
        $formIdProperty = $reflection->getProperty('formId');
        
        $miniProgramIdType = $miniProgramIdProperty->getType();
        $formIdType = $formIdProperty->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $miniProgramIdType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $formIdType);
        $this->assertSame('string', $miniProgramIdType->getName());
        $this->assertSame('string', $formIdType->getName());
    }

    public function test_execute_with_valid_mini_program_creates_form_id(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setName('Test Mini Program');
        $user = new User();
        $currentUser = $this->createMock(UserInterface::class); // Mock authenticated user
        
        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_123');
        $formId->setExpireTime(new \DateTime('+7 days'));

        $this->procedure->miniProgramId = '1';
        $this->procedure->formId = 'test_form_id_123';

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(MiniProgram::class, '1')
            ->willReturn($miniProgram);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $this->userService
            ->expects($this->once())
            ->method('getAlipayUser')
            ->with($currentUser)
            ->willReturn($user);

        $this->formIdService
            ->expects($this->once())
            ->method('saveFormId')
            ->with($miniProgram, $user, 'test_form_id_123')
            ->willReturn($formId);

        $result = $this->procedure->execute();
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('expiredTime', $result);
        $this->assertSame($formId->getId(), $result['id']);
    }

    public function test_execute_with_nonexistent_mini_program_throws_exception(): void
    {
        $this->procedure->miniProgramId = '999';
        $this->procedure->formId = 'test_form_id_123';

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(MiniProgram::class, '999')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('小程序不存在');

        $this->procedure->execute();
    }

    public function test_constructor_requires_dependencies(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);
        
        $this->assertSame('formIdService', $parameters[0]->getName());
        $this->assertSame('entityManager', $parameters[1]->getName());
        $this->assertSame('userService', $parameters[2]->getName());
        $this->assertSame('security', $parameters[3]->getName());
    }

    public function test_procedure_has_correct_attributes(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $attributes = $reflection->getAttributes();
        
        $attributeNames = array_map(fn($attr) => $attr->getName(), $attributes);
        
        $this->assertContains('Tourze\JsonRPC\Core\Attribute\MethodTag', $attributeNames);
        $this->assertContains('Tourze\JsonRPC\Core\Attribute\MethodDoc', $attributeNames);
        $this->assertContains('Tourze\JsonRPC\Core\Attribute\MethodExpose', $attributeNames);
        $this->assertContains('Tourze\JsonRPCLogBundle\Attribute\Log', $attributeNames);
        $this->assertContains('Symfony\Component\Security\Http\Attribute\IsGranted', $attributeNames);
    }

    public function test_procedure_extends_lockable_procedure(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $parentClass = $reflection->getParentClass();
        
        $this->assertNotFalse($parentClass);
        $this->assertSame(LockableProcedure::class, $parentClass->getName());
    }


    public function test_execute_method_returns_array(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $executeMethod = $reflection->getMethod('execute');
        
        $returnType = $executeMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('array', $returnType->getName());
    }

    public function test_procedure_properties_have_method_param_attributes(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        
        $miniProgramIdProperty = $reflection->getProperty('miniProgramId');
        $formIdProperty = $reflection->getProperty('formId');
        
        $miniProgramIdAttrs = $miniProgramIdProperty->getAttributes();
        $formIdAttrs = $formIdProperty->getAttributes();
        
        $this->assertGreaterThan(0, count($miniProgramIdAttrs));
        $this->assertGreaterThan(0, count($formIdAttrs));
    }

    public function test_execute_with_valid_data_includes_expire_time_in_response(): void
    {
        $miniProgram = new MiniProgram();
        $user = new User();
        $currentUser = $this->createMock(UserInterface::class);
        
        $expireTime = new \DateTime('+7 days');
        $formId = new FormId();
        $formId->setExpireTime($expireTime);

        $this->procedure->miniProgramId = '1';
        $this->procedure->formId = 'test_form_id_123';

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->willReturn($miniProgram);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $this->userService
            ->expects($this->once())
            ->method('getAlipayUser')
            ->willReturn($user);

        $this->formIdService
            ->expects($this->once())
            ->method('saveFormId')
            ->willReturn($formId);

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('expiredTime', $result);
        $this->assertSame($expireTime->format('Y-m-d H:i:s'), $result['expiredTime']);
    }

    public function test_procedure_dependency_injection(): void
    {
        $reflection = new \ReflectionClass($this->procedure);
        $constructor = $reflection->getConstructor();
        
        $parameters = $constructor->getParameters();
        
        // 检查所有参数都是只读的
        foreach ($parameters as $parameter) {
            $this->assertTrue($parameter->isPromoted(), "Parameter {$parameter->getName()} should be promoted");
        }
    }

    public function test_execute_calls_correct_service_methods(): void
    {
        $miniProgram = new MiniProgram();
        $user = new User();
        $currentUser = $this->createMock(UserInterface::class);
        $formId = new FormId();

        $this->procedure->miniProgramId = '1';
        $this->procedure->formId = 'test_form_id_123';

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(MiniProgram::class, '1')
            ->willReturn($miniProgram);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($currentUser);

        $this->userService
            ->expects($this->once())
            ->method('getAlipayUser')
            ->with($currentUser)
            ->willReturn($user);

        $this->formIdService
            ->expects($this->once())
            ->method('saveFormId')
            ->with($miniProgram, $user, 'test_form_id_123')
            ->willReturn($formId);

        $this->procedure->execute();
    }
} 