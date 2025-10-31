<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Phone::class)]
final class PhoneTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Phone();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'number' => ['number', '13800138000'],
        ];
    }

    public function testConstructorInitializesCollections(): void
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
        $phone->addUserPhone($userPhone);

        // Assert

        $this->assertTrue($phone->getUserPhones()->contains($userPhone));
        $this->assertSame($phone, $userPhone->getPhone());
    }

    public function testAddUserPhonePreventsDuplicates(): void
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
        $phone->removeUserPhone($userPhone);

        // Assert

        $this->assertFalse($phone->getUserPhones()->contains($userPhone));
        $this->assertNull($userPhone->getPhone());
    }

    public function testRemoveUserPhoneWhenNotExists(): void
    {
        // Arrange
        $phone = new Phone();
        $userPhone = new AlipayUserPhone();

        // Act
        $phone->removeUserPhone($userPhone);

        // Assert

        $this->assertCount(0, $phone->getUserPhones());
    }

    public function testRemoveUserPhoneWhenPhoneIsNotThis(): void
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
}
