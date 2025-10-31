<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AuthCode::class)]
final class AuthCodeTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AuthCode();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $authStart = new \DateTimeImmutable();

        return [
            'authCode' => ['authCode', 'test_auth_code_123'],
            'scope' => ['scope', AlipayAuthScope::AUTH_USER],
            'state' => ['state', 'test_state'],
            'userId' => ['userId', 'test_user_id'],
            'openId' => ['openId', 'test_open_id'],
            'accessToken' => ['accessToken', 'test_access_token'],
            'refreshToken' => ['refreshToken', 'test_refresh_token'],
            'expiresIn' => ['expiresIn', 7200],
            'reExpiresIn' => ['reExpiresIn', 2592000],
            'authStart' => ['authStart', $authStart],
            'sign' => ['sign', 'test_sign'],
        ];
    }

    public function testSetAlipayUserWithNullValue(): void
    {
        // Arrange
        $authCode = new AuthCode();

        // Act
        $authCode->setAlipayUser(null);

        // Assert

        $this->assertNull($authCode->getAlipayUser());
    }

    public function testScopeWithAllValues(): void
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
}
