<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $phone = new Phone();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        // Act & Assert
        $this->assertEquals(0, $phone->getId());

        $phone->setNumber('13800138000');
        $this->assertEquals('13800138000', $phone->getNumber());

        $phone->setCreateTime($createTime);
        $this->assertSame($createTime, $phone->getCreateTime());

        $phone->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $phone->getUpdateTime());
    }

    public function testConstructor_initializesCollections(): void
    {
        // Arrange & Act
        $phone = new Phone();

        // Assert
        $this->assertInstanceOf(ArrayCollection::class, $phone->getUserPhones());
        $this->assertCount(0, $phone->getUserPhones());
    }

    public function testAddUserPhone(): void
    {
        // Arrange
        $phone = new Phone();
        $userPhone = new AlipayUserPhone();

        // Act
        $result = $phone->addUserPhone($userPhone);

        // Assert
        $this->assertSame($phone, $result); // Test fluent interface
        $this->assertTrue($phone->getUserPhones()->contains($userPhone));
        $this->assertSame($phone, $userPhone->getPhone());
    }

    public function testAddUserPhone_preventsDuplicates(): void
    {
        // Arrange
        $phone = new Phone();
        $userPhone = new AlipayUserPhone();

        // Act
        $phone->addUserPhone($userPhone);
        $phone->addUserPhone($userPhone); // Add same userPhone again

        // Assert
        $this->assertCount(1, $phone->getUserPhones());
        $this->assertTrue($phone->getUserPhones()->contains($userPhone));
    }

    public function testRemoveUserPhone(): void
    {
        // Arrange
        $phone = new Phone();
        $userPhone = new AlipayUserPhone();
        $phone->addUserPhone($userPhone);

        // Act
        $result = $phone->removeUserPhone($userPhone);

        // Assert
        $this->assertSame($phone, $result); // Test fluent interface
        $this->assertFalse($phone->getUserPhones()->contains($userPhone));
        $this->assertNull($userPhone->getPhone());
    }

    public function testRemoveUserPhone_whenNotExists(): void
    {
        // Arrange
        $phone = new Phone();
        $userPhone = new AlipayUserPhone();

        // Act
        $result = $phone->removeUserPhone($userPhone);

        // Assert
        $this->assertSame($phone, $result); // Test fluent interface
        $this->assertCount(0, $phone->getUserPhones());
    }

    public function testRemoveUserPhone_whenPhoneIsNotThis(): void
    {
        // Arrange
        $phone = new Phone();
        $otherPhone = new Phone();
        $userPhone = new AlipayUserPhone();
        $userPhone->setPhone($otherPhone);
        $phone->getUserPhones()->add($userPhone);

        // Act
        $phone->removeUserPhone($userPhone);

        // Assert
        $this->assertSame($otherPhone, $userPhone->getPhone()); // Should not change
    }

    public function testSetNumber_withValidPhoneNumber(): void
    {
        // Arrange
        $phone = new Phone();

        // Act
        $result = $phone->setNumber('13800138000');

        // Assert
        $this->assertSame($phone, $result); // Test fluent interface
        $this->assertEquals('13800138000', $phone->getNumber());
    }
}
