<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\SaveAlipayMiniProgramFormId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

/**
 * @internal
 */
#[CoversClass(SaveAlipayMiniProgramFormId::class)]
#[RunTestsInSeparateProcesses]
final class SaveAlipayMiniProgramFormIdTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 结构性测试，不需要初始化
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }

    public function testProcedureHasRequiredProperties(): void
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

    public function testHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);

        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('getLockResource'));
        $this->assertTrue($reflection->hasMethod('getIdempotentCacheKey'));
    }

    public function testConstructorRequiresDependencies(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);

        $this->assertSame('formIdService', $parameters[0]->getName());
        $this->assertSame('entityManager', $parameters[1]->getName());
        $this->assertSame('userService', $parameters[2]->getName());
        $this->assertSame('security', $parameters[3]->getName());
    }

    public function testProcedureHasCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $attributes = $reflection->getAttributes();

        $attributeNames = array_map(fn ($attr) => $attr->getName(), $attributes);

        $this->assertContains('Tourze\JsonRPCLogBundle\Attribute\Log', $attributeNames);
        $this->assertContains('Symfony\Component\Security\Http\Attribute\IsGranted', $attributeNames);
    }

    public function testProcedureExtendsLockableProcedure(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(LockableProcedure::class, $parentClass->getName());
    }

    public function testExecuteMethodReturnsArray(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $executeMethod = $reflection->getMethod('execute');

        $returnType = $executeMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('array', $returnType->getName());
    }

    public function testProcedurePropertiesHaveMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);

        $miniProgramIdProperty = $reflection->getProperty('miniProgramId');
        $formIdProperty = $reflection->getProperty('formId');

        $miniProgramIdAttrs = $miniProgramIdProperty->getAttributes();
        $formIdAttrs = $formIdProperty->getAttributes();

        $this->assertGreaterThan(0, count($miniProgramIdAttrs));
        $this->assertGreaterThan(0, count($formIdAttrs));
    }

    public function testConstructorHasProperPromotedParameters(): void
    {
        $reflection = new \ReflectionClass(SaveAlipayMiniProgramFormId::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();

        // 检查所有参数都是只读的
        foreach ($parameters as $parameter) {
            $this->assertTrue($parameter->isPromoted(), "Parameter {$parameter->getName()} should be promoted");
        }
    }
}
