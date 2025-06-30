<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramAuthCode;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use ReflectionClass;

/**
 * UploadAlipayMiniProgramAuthCode 的单元测试
 */
class UploadAlipayMiniProgramAuthCodeTest extends TestCase
{
    /**
     * 测试类继承正确的基类
     */
    public function testExtendsLockableProcedure(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }

    /**
     * 测试类有必要的属性
     */
    public function testHasRequiredProperties(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        
        $this->assertTrue($reflection->hasProperty('appId'));
        $this->assertTrue($reflection->hasProperty('scope'));
        $this->assertTrue($reflection->hasProperty('authCode'));
    }

    /**
     * 测试类有必要的方法
     */
    public function testHasRequiredMethods(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        
        $this->assertTrue($reflection->hasMethod('execute'));
        $this->assertTrue($reflection->hasMethod('getLockResource'));
        $this->assertTrue($reflection->hasMethod('getIdempotentCacheKey'));
    }

    /**
     * 测试方法属性
     */
    public function testMethodAttributes(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
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

    /**
     * 测试参数属性
     */
    public function testParameterAttributes(): void
    {
        $reflection = new ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        
        // 检查 appId 属性
        $appIdProperty = $reflection->getProperty('appId');
        $appIdAttributes = $appIdProperty->getAttributes();
        $hasMethodParam = false;
        foreach ($appIdAttributes as $attribute) {
            if (str_contains($attribute->getName(), 'MethodParam')) {
                $hasMethodParam = true;
                break;
            }
        }
        $this->assertTrue($hasMethodParam, 'appId property should have MethodParam attribute');
        
        // 检查 scope 属性
        $scopeProperty = $reflection->getProperty('scope');
        $scopeAttributes = $scopeProperty->getAttributes();
        $hasMethodParam = false;
        $hasAssert = false;
        foreach ($scopeAttributes as $attribute) {
            $name = $attribute->getName();
            if (str_contains($name, 'MethodParam')) {
                $hasMethodParam = true;
            }
            if (str_contains($name, 'Assert') || str_contains($name, 'NotNull')) {
                $hasAssert = true;
            }
        }
        $this->assertTrue($hasMethodParam, 'scope property should have MethodParam attribute');
        $this->assertTrue($hasAssert, 'scope property should have Assert attribute');
        
        // 检查 authCode 属性
        $authCodeProperty = $reflection->getProperty('authCode');
        $authCodeAttributes = $authCodeProperty->getAttributes();
        $hasMethodParam = false;
        $hasAssert = false;
        foreach ($authCodeAttributes as $attribute) {
            $name = $attribute->getName();
            if (str_contains($name, 'MethodParam')) {
                $hasMethodParam = true;
            }
            if (str_contains($name, 'Assert') || str_contains($name, 'NotNull')) {
                $hasAssert = true;
            }
        }
        $this->assertTrue($hasMethodParam, 'authCode property should have MethodParam attribute');
        $this->assertTrue($hasAssert, 'authCode property should have Assert attribute');
    }
}