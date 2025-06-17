<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use PHPUnit\Framework\TestCase;

class MiniProgramTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        // Act & Assert
        $this->assertEquals(0, $miniProgram->getId());

        $miniProgram->setCode('test_code_123');
        $this->assertEquals('test_code_123', $miniProgram->getCode());

        $miniProgram->setName('测试小程序');
        $this->assertEquals('测试小程序', $miniProgram->getName());

        $miniProgram->setAppId('2021001234567890');
        $this->assertEquals('2021001234567890', $miniProgram->getAppId());

        $miniProgram->setPrivateKey('-----BEGIN PRIVATE KEY-----');
        $this->assertEquals('-----BEGIN PRIVATE KEY-----', $miniProgram->getPrivateKey());

        $miniProgram->setAlipayPublicKey('-----BEGIN PUBLIC KEY-----');
        $this->assertEquals('-----BEGIN PUBLIC KEY-----', $miniProgram->getAlipayPublicKey());

        $miniProgram->setEncryptKey('test_encrypt_key');
        $this->assertEquals('test_encrypt_key', $miniProgram->getEncryptKey());

        $miniProgram->setSandbox(true);
        $this->assertTrue($miniProgram->isSandbox());

        $miniProgram->setSignType('RSA2');
        $this->assertEquals('RSA2', $miniProgram->getSignType());

        // 首先确保不是沙箱环境
        $miniProgram->setSandbox(false);
        $miniProgram->setGatewayUrl('https://openapi.alipay.com/gateway.do');
        $this->assertEquals('https://openapi.alipay.com/gateway.do', $miniProgram->getGatewayUrl());

        $miniProgram->setAuthRedirectUrl('https://example.com/auth/callback');
        $this->assertEquals('https://example.com/auth/callback', $miniProgram->getAuthRedirectUrl());

        $miniProgram->setRemark('测试备注');
        $this->assertEquals('测试备注', $miniProgram->getRemark());

        $miniProgram->setCreateTime($createTime);
        $this->assertSame($createTime, $miniProgram->getCreateTime());

        $miniProgram->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $miniProgram->getUpdateTime());
    }

    public function testGetGatewayUrl_withSandboxEnabled(): void
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

    public function testGetGatewayUrl_withSandboxDisabled(): void
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

    public function testSetEncryptKey_withNullValue(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();

        // Act
        $result = $miniProgram->setEncryptKey(null);

        // Assert
        $this->assertSame($miniProgram, $result); // Test fluent interface
        $this->assertNull($miniProgram->getEncryptKey());
    }

    public function testSetAuthRedirectUrl_withNullValue(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();

        // Act
        $result = $miniProgram->setAuthRedirectUrl(null);

        // Assert
        $this->assertSame($miniProgram, $result); // Test fluent interface
        $this->assertNull($miniProgram->getAuthRedirectUrl());
    }

    public function testSetRemark_withNullValue(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();

        // Act
        $result = $miniProgram->setRemark(null);

        // Assert
        $this->assertSame($miniProgram, $result); // Test fluent interface
        $this->assertNull($miniProgram->getRemark());
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

    public function testFluentInterface(): void
    {
        // Arrange
        $miniProgram = new MiniProgram();

        // Act & Assert - Test that all setters return the entity instance
        $this->assertSame($miniProgram, $miniProgram->setCode('test'));
        $this->assertSame($miniProgram, $miniProgram->setName('test'));
        $this->assertSame($miniProgram, $miniProgram->setAppId('test'));
        $this->assertSame($miniProgram, $miniProgram->setPrivateKey('test'));
        $this->assertSame($miniProgram, $miniProgram->setAlipayPublicKey('test'));
        $this->assertSame($miniProgram, $miniProgram->setEncryptKey('test'));
        $this->assertSame($miniProgram, $miniProgram->setSandbox(true));
        $this->assertSame($miniProgram, $miniProgram->setSignType('RSA'));
        $this->assertSame($miniProgram, $miniProgram->setGatewayUrl('test'));
        $this->assertSame($miniProgram, $miniProgram->setAuthRedirectUrl('test'));
        $this->assertSame($miniProgram, $miniProgram->setRemark('test'));
    }
}
