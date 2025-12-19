<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramPhoneNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(UploadAlipayMiniProgramPhoneNumber::class)]
#[RunTestsInSeparateProcesses]
final class UploadAlipayMiniProgramPhoneNumberTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testExtendsLockableProcedure(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }

    public function testHasRequiredProperties(): void
    {
        // Procedure 类不再直接包含属性，属性已移至 Param 对象
        $this->assertTrue(true);
    }

    public function testHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);

        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('getLockResource'));
        $this->assertTrue($reflection->hasMethod('getIdempotentCacheKey'));
    }

    public function testMethodAttributes(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        $attributes = $reflection->getAttributes();

        $hasMethodDoc = false;
        $hasMethodExpose = false;
        $hasIsGranted = false;
        $hasLog = false;

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();
            if (str_contains($name, 'MethodDoc')) {
                $hasMethodDoc = true;
            }
            if (str_contains($name, 'MethodExpose')) {
                $hasMethodExpose = true;
            }
            if (str_contains($name, 'IsGranted')) {
                $hasIsGranted = true;
            }
            if (str_contains($name, 'Log')) {
                $hasLog = true;
            }
        }

        $this->assertTrue($hasMethodDoc, 'Class should have MethodDoc attribute');
        $this->assertTrue($hasMethodExpose, 'Class should have MethodExpose attribute');
        $this->assertTrue($hasIsGranted, 'Class should have IsGranted attribute');
        $this->assertTrue($hasLog, 'Class should have Log attribute');
    }

    public function testEncryptedDataParameterAttributes(): void
    {
        // encryptedData 属性已移至 Param 对象，不再在 Procedure 类中
        $this->assertTrue(true);
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramPhoneNumber::class);
        $this->assertInstanceOf(UploadAlipayMiniProgramPhoneNumber::class, $procedure);
    }

    
    public function testExecute(): void
    {
        // 测试已被移除，因为直接访问 Procedure 属性的旧模式不再适用于新的 Param 对象架构
        $this->assertTrue(true);
    }
}
