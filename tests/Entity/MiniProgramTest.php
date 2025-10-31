<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(MiniProgram::class)]
final class MiniProgramTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MiniProgram();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'code' => ['code', 'test_code_123'],
            'name' => ['name', '测试小程序'],
            'appId' => ['appId', '2021001234567890'],
            'privateKey' => ['privateKey', '-----BEGIN PRIVATE KEY-----'],
            'alipayPublicKey' => ['alipayPublicKey', '-----BEGIN PUBLIC KEY-----'],
            'encryptKey' => ['encryptKey', 'test_encrypt_key'],
            'sandbox' => ['sandbox', true],
            'signType' => ['signType', 'RSA2'],
            'gatewayUrl' => ['gatewayUrl', 'https://openapi.alipay.com/gateway.do'],
            'authRedirectUrl' => ['authRedirectUrl', 'https://example.com/auth/callback'],
            'remark' => ['remark', '测试备注'],
        ];
    }

    public function testGetGatewayUrlWithSandboxEnabled(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();
        $miniProgram->setSandbox(true);
        $miniProgram->setGatewayUrl('https://openapi.alipay.com/gateway.do');

        // Act
        $result = $miniProgram->getGatewayUrl();

        // Assert
        $this->assertEquals('https://openapi.alipaydev.com/gateway.do', $result);
    }

    public function testGetGatewayUrlWithSandboxDisabled(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();
        $miniProgram->setSandbox(false);
        $miniProgram->setGatewayUrl('https://openapi.alipay.com/gateway.do');

        // Act
        $result = $miniProgram->getGatewayUrl();

        // Assert
        $this->assertEquals('https://openapi.alipay.com/gateway.do', $result);
    }

    public function testDefaultValues(): void
    {
        // Arrange & Act
        $miniProgram = new MiniProgram();

        // Assert
        $this->assertFalse($miniProgram->isSandbox());
        $this->assertEquals('RSA2', $miniProgram->getSignType());
        $this->assertEquals('https://openapi.alipay.com/gateway.do', $miniProgram->getGatewayUrl());
    }
}
