<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $user = new User();
        $miniProgram = new MiniProgram();
        $lastInfoUpdateTime = new \DateTime();
        $createTime = new \DateTime();
        $updateTime = new \DateTime();

        // Act & Assert
        $this->assertEquals(0, $user->getId());

        $user->setMiniProgram($miniProgram);
        $this->assertSame($miniProgram, $user->getMiniProgram());

        $user->setOpenId('test_open_id_123');
        $this->assertEquals('test_open_id_123', $user->getOpenId());

        $user->setNickName('测试用户');
        $this->assertEquals('测试用户', $user->getNickName());

        $user->setAvatar('https://example.com/avatar.jpg');
        $this->assertEquals('https://example.com/avatar.jpg', $user->getAvatar());

        $user->setProvince('广东省');
        $this->assertEquals('广东省', $user->getProvince());

        $user->setCity('深圳市');
        $this->assertEquals('深圳市', $user->getCity());

        $user->setGender(AlipayUserGender::MALE);
        $this->assertEquals(AlipayUserGender::MALE, $user->getGender());

        $user->setLastInfoUpdateTime($lastInfoUpdateTime);
        $this->assertSame($lastInfoUpdateTime, $user->getLastInfoUpdateTime());

        $user->setCreateTime($createTime);
        $this->assertSame($createTime, $user->getCreateTime());

        $user->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $user->getUpdateTime());
    }

    public function testConstructor_initializesCollections(): void
    {
        // Arrange & Act
        $user = new User();

        // Assert
        $this->assertInstanceOf(ArrayCollection::class, $user->getAuthCodes());
        $this->assertCount(0, $user->getAuthCodes());
        $this->assertInstanceOf(ArrayCollection::class, $user->getUserPhones());
        $this->assertCount(0, $user->getUserPhones());
    }

    public function testAddAuthCode(): void
    {
        // Arrange
        $user = new User();
        $authCode = new AuthCode();

        // Act
        $result = $user->addAuthCode($authCode);

        // Assert
        $this->assertSame($user, $result); // Test fluent interface
        $this->assertTrue($user->getAuthCodes()->contains($authCode));
        $this->assertSame($user, $authCode->getAlipayUser());
    }

    public function testAddAuthCode_preventsDuplicates(): void
    {
        // Arrange
        $user = new User();
        $authCode = new AuthCode();

        // Act
        $user->addAuthCode($authCode);
        $user->addAuthCode($authCode); // Add same authCode again

        // Assert
        $this->assertCount(1, $user->getAuthCodes());
        $this->assertTrue($user->getAuthCodes()->contains($authCode));
    }

    public function testRemoveAuthCode(): void
    {
        // Arrange
        $user = new User();
        $authCode = new AuthCode();
        $user->addAuthCode($authCode);

        // Act
        $result = $user->removeAuthCode($authCode);

        // Assert
        $this->assertSame($user, $result); // Test fluent interface
        $this->assertFalse($user->getAuthCodes()->contains($authCode));
        $this->assertNull($authCode->getAlipayUser());
    }

    public function testGetLatestAuthCode_whenExists(): void
    {
        // Arrange
        $user = new User();
        $authCode1 = new AuthCode();
        $authCode2 = new AuthCode();
        $user->addAuthCode($authCode1);
        $user->addAuthCode($authCode2);

        // Act
        $result = $user->getLatestAuthCode();

        // Assert
        $this->assertSame($authCode2, $result); // Should return the last added
    }

    public function testGetLatestAuthCode_whenEmpty(): void
    {
        // Arrange
        $user = new User();

        // Act
        $result = $user->getLatestAuthCode();

        // Assert
        $this->assertNull($result);
    }

    public function testAddUserPhone(): void
    {
        // Arrange
        $user = new User();
        $userPhone = new AlipayUserPhone();

        // Act
        $result = $user->addUserPhone($userPhone);

        // Assert
        $this->assertSame($user, $result); // Test fluent interface
        $this->assertTrue($user->getUserPhones()->contains($userPhone));
        $this->assertSame($user, $userPhone->getUser());
    }

    public function testRemoveUserPhone(): void
    {
        // Arrange
        $user = new User();
        $userPhone = new AlipayUserPhone();
        $user->addUserPhone($userPhone);

        // Act
        $result = $user->removeUserPhone($userPhone);

        // Assert
        $this->assertSame($user, $result); // Test fluent interface
        $this->assertFalse($user->getUserPhones()->contains($userPhone));
        $this->assertNull($userPhone->getUser());
    }

    public function testGetLatestPhone_whenExists(): void
    {
        // Arrange
        $user = new User();
        $phone1 = new Phone();
        $phone1->setNumber('13800138000');
        $phone2 = new Phone();
        $phone2->setNumber('13900139000');

        $userPhone1 = new AlipayUserPhone();
        $userPhone1->setPhone($phone1);
        $userPhone2 = new AlipayUserPhone();
        $userPhone2->setPhone($phone2);

        $user->addUserPhone($userPhone1);
        $user->addUserPhone($userPhone2);

        // Act
        $result = $user->getLatestPhone();

        // Assert
        $this->assertSame($phone2, $result); // Should return the last added phone
    }

    public function testGetLatestPhone_whenEmpty(): void
    {
        // Arrange
        $user = new User();

        // Act
        $result = $user->getLatestPhone();

        // Assert
        $this->assertNull($result);
    }

    public function testSetGender_withAllValues(): void
    {
        // Arrange
        $user = new User();

        // Test MALE
        $user->setGender(AlipayUserGender::MALE);
        $this->assertEquals(AlipayUserGender::MALE, $user->getGender());

        // Test FEMALE
        $user->setGender(AlipayUserGender::FEMALE);
        $this->assertEquals(AlipayUserGender::FEMALE, $user->getGender());

        // Test null
        $user->setGender(null);
        $this->assertNull($user->getGender());
    }

    public function testNullableFields(): void
    {
        // Arrange & Act
        $user = new User();

        // Assert default values
        $this->assertNull($user->getNickName());
        $this->assertNull($user->getAvatar());
        $this->assertNull($user->getProvince());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getGender());
        $this->assertNull($user->getLastInfoUpdateTime());

        // Test setting null values
        $user->setNickName(null);
        $user->setAvatar(null);
        $user->setProvince(null);
        $user->setCity(null);
        $user->setGender(null);
        $user->setLastInfoUpdateTime(null);

        $this->assertNull($user->getNickName());
        $this->assertNull($user->getAvatar());
        $this->assertNull($user->getProvince());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getGender());
        $this->assertNull($user->getLastInfoUpdateTime());
    }

    public function testFluentInterface(): void
    {
        // Arrange
        $user = new User();
        $miniProgram = new MiniProgram();

        // Act & Assert - Test that all setters return the entity instance
        $this->assertSame($user, $user->setMiniProgram($miniProgram));
        $this->assertSame($user, $user->setOpenId('test'));
        $this->assertSame($user, $user->setNickName('test'));
        $this->assertSame($user, $user->setAvatar('test'));
        $this->assertSame($user, $user->setProvince('test'));
        $this->assertSame($user, $user->setCity('test'));
        $this->assertSame($user, $user->setGender(AlipayUserGender::MALE));
        $this->assertSame($user, $user->setLastInfoUpdateTime(new \DateTime()));
    }
}
