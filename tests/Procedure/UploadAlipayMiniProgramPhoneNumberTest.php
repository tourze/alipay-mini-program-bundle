<?php

namespace AlipayMiniProgramBundle\Tests\Procedure;

use AlipayMiniProgramBundle\Procedure\UploadAlipayMiniProgramPhoneNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

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
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);

        $this->assertTrue($reflection->hasProperty('encryptedData'));
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
        $reflection = new \ReflectionClass(UploadAlipayMiniProgramPhoneNumber::class);
        $encryptedDataProperty = $reflection->getProperty('encryptedData');

        $attributes = $encryptedDataProperty->getAttributes();
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

        $this->assertTrue($hasMethodParam, 'encryptedData property should have MethodParam attribute');
        $this->assertTrue($hasAssert, 'encryptedData property should have Assert attribute');
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramPhoneNumber::class);
        $this->assertInstanceOf(UploadAlipayMiniProgramPhoneNumber::class, $procedure);
    }

    public function testExecuteWithoutAuthentication(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramPhoneNumber::class);

        $procedure->encryptedData = 'test_encrypted_data';

        $this->expectException(ApiException::class);

        $procedure->execute();
    }

    public function testProcedureParamsAreSetCorrectly(): void
    {
        $procedure = self::getService(UploadAlipayMiniProgramPhoneNumber::class);

        $procedure->encryptedData = 'test_encrypted_data';

        $this->assertEquals('test_encrypted_data', $procedure->encryptedData);
    }
}
