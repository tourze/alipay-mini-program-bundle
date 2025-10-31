<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class UserTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // User has required relations, so we need to create a minimal valid entity
        $miniProgram = new MiniProgram();
        $miniProgram->setName('Test MiniProgram');
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setPrivateKey('test_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_123');

        return $user;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'openId' => ['openId', 'test_open_id_123'],
            'nickName' => ['nickName', '测试用户'],
            'avatar' => ['avatar', 'https://example.com/avatar.jpg'],
            'province' => ['province', '广东省'],
            'city' => ['city', '深圳市'],
            'gender' => ['gender', AlipayUserGender::MALE],
            'lastInfoUpdateTime' => ['lastInfoUpdateTime', new \DateTimeImmutable()],
        ];
    }

    public function testConstructorInitializesCollections(): void
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
        $user->addAuthCode($authCode);

        // Assert

        $this->assertTrue($user->getAuthCodes()->contains($authCode));
        $this->assertSame($user, $authCode->getAlipayUser());
    }

    public function testAddAuthCodePreventsDuplicates(): void
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
        $user->removeAuthCode($authCode);

        // Assert

        $this->assertFalse($user->getAuthCodes()->contains($authCode));
        $this->assertNull($authCode->getAlipayUser());
    }

    public function testGetLatestAuthCodeWhenExists(): void
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

    public function testGetLatestAuthCodeWhenEmpty(): void
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
        $user->addUserPhone($userPhone);

        // Assert

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
        $user->removeUserPhone($userPhone);

        // Assert

        $this->assertFalse($user->getUserPhones()->contains($userPhone));
        $this->assertNull($userPhone->getUser());
    }

    public function testGetLatestPhoneWhenExists(): void
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

    public function testGetLatestPhoneWhenEmpty(): void
    {
        // Arrange
        $user = new User();

        // Act
        $result = $user->getLatestPhone();

        // Assert
        $this->assertNull($result);
    }

    public function testSetGenderWithAllValues(): void
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
}
