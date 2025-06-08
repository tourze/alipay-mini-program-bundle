<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use PHPUnit\Framework\TestCase;

class AuthCodeTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $authCode = new AuthCode();
        $user = new User();
        $authStart = new \DateTime();
        $createTime = new \DateTime();
        $updateTime = new \DateTime();

        // Act & Assert
        $this->assertEquals(0, $authCode->getId());

        $authCode->setAlipayUser($user);
        $this->assertSame($user, $authCode->getAlipayUser());

        $authCode->setAuthCode('test_auth_code_123');
        $this->assertEquals('test_auth_code_123', $authCode->getAuthCode());

        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $this->assertEquals(AlipayAuthScope::AUTH_USER, $authCode->getScope());

        $authCode->setState('test_state');
        $this->assertEquals('test_state', $authCode->getState());

        $authCode->setUserId('test_user_id');
        $this->assertEquals('test_user_id', $authCode->getUserId());

        $authCode->setOpenId('test_open_id');
        $this->assertEquals('test_open_id', $authCode->getOpenId());

        $authCode->setAccessToken('test_access_token');
        $this->assertEquals('test_access_token', $authCode->getAccessToken());

        $authCode->setRefreshToken('test_refresh_token');
        $this->assertEquals('test_refresh_token', $authCode->getRefreshToken());

        $authCode->setExpiresIn(7200);
        $this->assertEquals(7200, $authCode->getExpiresIn());

        $authCode->setReExpiresIn(2592000);
        $this->assertEquals(2592000, $authCode->getReExpiresIn());

        $authCode->setAuthStart($authStart);
        $this->assertSame($authStart, $authCode->getAuthStart());

        $authCode->setSign('test_sign');
        $this->assertEquals('test_sign', $authCode->getSign());

        $authCode->setCreatedFromIp('192.168.1.1');
        $this->assertEquals('192.168.1.1', $authCode->getCreatedFromIp());

        $authCode->setUpdatedFromIp('192.168.1.2');
        $this->assertEquals('192.168.1.2', $authCode->getUpdatedFromIp());

        $authCode->setCreateTime($createTime);
        $this->assertSame($createTime, $authCode->getCreateTime());

        $authCode->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $authCode->getUpdateTime());
    }

    public function testSetAlipayUser_withNullValue(): void
    {
        // Arrange
        $authCode = new AuthCode();

        // Act
        $result = $authCode->setAlipayUser(null);

        // Assert
        $this->assertSame($authCode, $result); // Test fluent interface
        $this->assertNull($authCode->getAlipayUser());
    }

    public function testSetState_withNullValue(): void
    {
        // Arrange
        $authCode = new AuthCode();

        // Act
        $result = $authCode->setState(null);

        // Assert
        $this->assertSame($authCode, $result); // Test fluent interface
        $this->assertNull($authCode->getState());
    }

    public function testSetSign_withNullValue(): void
    {
        // Arrange
        $authCode = new AuthCode();

        // Act
        $result = $authCode->setSign(null);

        // Assert
        $this->assertSame($authCode, $result); // Test fluent interface
        $this->assertNull($authCode->getSign());
    }

    public function testScope_withAllValues(): void
    {
        // Arrange
        $authCode = new AuthCode();

        // Test AUTH_BASE
        $authCode->setScope(AlipayAuthScope::AUTH_BASE);
        $this->assertEquals(AlipayAuthScope::AUTH_BASE, $authCode->getScope());

        // Test AUTH_USER
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $this->assertEquals(AlipayAuthScope::AUTH_USER, $authCode->getScope());
    }

    public function testFluentInterface(): void
    {
        // Arrange
        $authCode = new AuthCode();
        $user = new User();

        // Act & Assert - Test that all setters return the entity instance
        $this->assertSame($authCode, $authCode->setAlipayUser($user));
        $this->assertSame($authCode, $authCode->setAuthCode('test'));
        $this->assertSame($authCode, $authCode->setScope(AlipayAuthScope::AUTH_BASE));
        $this->assertSame($authCode, $authCode->setState('test'));
        $this->assertSame($authCode, $authCode->setUserId('test'));
        $this->assertSame($authCode, $authCode->setOpenId('test'));
        $this->assertSame($authCode, $authCode->setAccessToken('test'));
        $this->assertSame($authCode, $authCode->setRefreshToken('test'));
        $this->assertSame($authCode, $authCode->setExpiresIn(3600));
        $this->assertSame($authCode, $authCode->setReExpiresIn(86400));
        $this->assertSame($authCode, $authCode->setAuthStart(new \DateTime()));
        $this->assertSame($authCode, $authCode->setSign('test'));
        $this->assertSame($authCode, $authCode->setCreatedFromIp('127.0.0.1'));
        $this->assertSame($authCode, $authCode->setUpdatedFromIp('127.0.0.1'));
    }
}
