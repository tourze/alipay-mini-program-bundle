<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class AlipayUserPhoneTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();
        $user = $this->createMock(User::class);
        $phone = $this->createMock(Phone::class);
        $verifiedTime = new \DateTimeImmutable();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        // Act & Assert
        $this->assertEquals(0, $userPhone->getId());

        $userPhone->setUser($user);
        $this->assertSame($user, $userPhone->getUser());

        $userPhone->setPhone($phone);
        $this->assertSame($phone, $userPhone->getPhone());

        $userPhone->setVerifiedTime($verifiedTime);
        $this->assertSame($verifiedTime, $userPhone->getVerifiedTime());

        $userPhone->setCreateTime($createTime);
        $this->assertSame($createTime, $userPhone->getCreateTime());

        $userPhone->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $userPhone->getUpdateTime());
    }

    public function testSetVerifiedTime_withDateTime(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();
        $verifiedTime = new \DateTime('2023-12-01 10:30:00');

        // Act
        $result = $userPhone->setVerifiedTime($verifiedTime);

        // Assert
        $this->assertSame($userPhone, $result); // Test fluent interface
        $retrievedTime = $userPhone->getVerifiedTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $retrievedTime);
        $this->assertEquals($verifiedTime->format('Y-m-d H:i:s'), $retrievedTime->format('Y-m-d H:i:s'));
    }

    public function testSetUser_withNullValue(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();

        // Act
        $result = $userPhone->setUser(null);

        // Assert
        $this->assertSame($userPhone, $result); // Test fluent interface
        $this->assertNull($userPhone->getUser());
    }

    public function testSetPhone_withNullValue(): void
    {
        // Arrange
        $userPhone = new AlipayUserPhone();

        // Act
        $result = $userPhone->setPhone(null);

        // Assert
        $this->assertSame($userPhone, $result); // Test fluent interface
        $this->assertNull($userPhone->getPhone());
    }
}
