<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramPhoneNumber;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use ReflectionClass;

/**
 * UploadAlipayMiniProgramPhoneNumber 的单元测试
 */
class UploadAlipayMiniProgramPhoneNumberTest extends TestCase
{
    /**
     * 测试类继承正确的基类
     */
    public function testExtendsLockableProcedure(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }

    /**
     * 测试类有必要的属性
     */
    public function testHasRequiredProperties(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        
        $this->assertTrue($reflection->hasProperty('encryptedData'));
    }

    /**
     * 测试类有必要的方法
     */
    public function testHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        
        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('getLockResource'));
        $this->assertTrue($reflection->hasMethod('getIdempotentCacheKey'));
    }

    /**
     * 测试方法属性
     */
    public function testMethodAttributes(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
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

    /**
     * 测试参数属性
     */
    public function testParameterAttributes(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        
        // 检查 encryptedData 属性
        $encryptedDataProperty = $reflection->getProperty('encryptedData');
        $encryptedDataAttributes = $encryptedDataProperty->getAttributes();
        $hasMethodParam = false;
        $hasAssert = false;
        foreach ($encryptedDataAttributes as $attribute) {
            $name = $attribute->getName();
            if (str_contains($name, 'MethodParam')) {
                $hasMethodParam = true;
            }
            if (str_contains($name, 'Assert') || str_contains($name, 'NotNull')) {
                $hasAssert = true;
            }
        }
        $this->assertTrue($hasMethodParam, 'encryptedData property should have MethodParam attribute');
        $this->assertTrue($hasAssert, 'encryptedData property should have Assert attribute');
    }
}