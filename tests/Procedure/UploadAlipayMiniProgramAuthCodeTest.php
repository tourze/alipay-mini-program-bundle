<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramAuthCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

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

    public function testHasRequiredProperties(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);

        $this->assertTrue($reflection->hasProperty('appId'));
        $this->assertTrue($reflection->hasProperty('scope'));
        $this->assertTrue($reflection->hasProperty('authCode'));
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

    public function testAppIdParameterAttributes(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $appIdProperty = $reflection->getProperty('appId');

        $attributes = $appIdProperty->getAttributes();
        $hasMethodParam = false;

        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'MethodParam')) {
                $hasMethodParam = true;
                break;
            }
        }

        $this->assertTrue($hasMethodParam, 'appId property should have MethodParam attribute');
    }

    public function testScopeParameterAttributes(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $scopeProperty = $reflection->getProperty('scope');

        $attributes = $scopeProperty->getAttributes();
        $hasMethodParam = false;
        $hasAssert = false;

        foreach ($attributes as $attribute) {
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
    }

    public function testAuthCodeParameterAttributes(): void
    {
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramAuthCode::class);
        $authCodeProperty = $reflection->getProperty('authCode');

        $attributes = $authCodeProperty->getAttributes();
        $hasMethodParam = false;
        $hasAssert = false;

        foreach ($attributes as $attribute) {
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

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramAuthCode::class);
        $this->assertInstanceOf(UploadAlipayMiniProgramAuthCode::class, $procedure);
    }

    public function testExecuteWithInvalidScope(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramAuthCode::class);

        $procedure->appId = 'test_app_id';
        $procedure->scope = 'invalid_scope';
        $procedure->authCode = 'test_auth_code';

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('无效的授权范围');

        $procedure->execute();
    }

    public function testProcedureParamsAreSetCorrectly(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramAuthCode::class);

        $procedure->appId = 'test_app_id';
        $procedure->scope = 'auth_base';
        $procedure->authCode = 'test_auth_code';

        $this->assertEquals('test_app_id', $procedure->appId);
        $this->assertEquals('auth_base', $procedure->scope);
        $this->assertEquals('test_auth_code', $procedure->authCode);
    }
}
