<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramAuthCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(UploadAlipayMiniProgramAuthCode::class)]
#[RunTestsInSeparateProcesses]
final class UploadAlipayMiniProgramAuthCodeTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }


    public function testHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);

        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('getLockResource'));
        $this->assertTrue($reflection->hasMethod('getIdempotentCacheKey'));
    }

    public function testMethodAttributes(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $attributes = $reflection->getAttributes();

        $hasMethodDoc = false;
        $hasMethodExpose = false;
        $hasLog = false;

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            if (str_contains($name, 'MethodDoc')) {
                $hasMethodDoc = true;
            }
            if (str_contains($name, 'MethodExpose')) {
                $hasMethodExpose = true;
            }
            if (str_contains($name, 'Log')) {
                $hasLog = true;
            }
        }

        $this->assertTrue($hasMethodDoc, 'Class should have MethodDoc attribute');
        $this->assertTrue($hasMethodExpose, 'Class should have MethodExpose attribute');
        $this->assertTrue($hasLog, 'Class should have Log attribute');
    }


    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramAuthCode::class);
        $this->assertInstanceOf(UploadAlipayMiniProgramAuthCode::class, $procedure);
    }

    /**
     * execute() 方法的完整业务逻辑测试需要复杂的外部依赖（支付宝 API、数据库等）。
     * 这里仅验证方法签名的存在性和返回类型。
     */
    public function testExecute(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $this->assertTrue($reflection->hasMethod('execute'));

        $executeMethod = $reflection->getMethod('execute');
        $returnType = $executeMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('Tourze\JsonRPC\Core\Result\ArrayResult', $returnType->getName());
    }
}
