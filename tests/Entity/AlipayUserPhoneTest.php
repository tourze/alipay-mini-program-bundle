<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayUserPhone::class)]
final class AlipayUserPhoneTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AlipayUserPhone();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'verifiedTime' => ['verifiedTime', new \DateTimeImmutable()],
        ];
    }

    public function testSetVerifiedTimeWithDateTime(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();
        $verifiedTime = new \DateTime('2023-12-01 10:30:00');

        // Act
        $userPhone->setVerifiedTime($verifiedTime);

        // Assert

        $retrievedTime = $userPhone->getVerifiedTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $retrievedTime);
        $this->assertEquals($verifiedTime->format('Y-m-d H:i:s'), $retrievedTime->format('Y-m-d H:i:s'));
    }

    public function testSetUserWithNullValue(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();

        // Act
        $userPhone->setUser(null);

        // Assert

        $this->assertNull($userPhone->getUser());
    }

    public function testSetPhoneWithNullValue(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();

        // Act
        $userPhone->setPhone(null);

        // Assert

        $this->assertNull($userPhone->getPhone());
    }
}
