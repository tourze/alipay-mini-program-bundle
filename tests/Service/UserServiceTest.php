<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Exception\UserNotFoundException;
use AlipayMiniProgramBundle\Repository\PhoneRepository;
use AlipayMiniProgramBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(UserService::class)]
#[RunTestsInSeparateProcesses]
final class UserServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testUpdateUserInfoWithAllFields(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setName('Test App');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);

        $userService->updateUserInfo($user, [
            'nick_name' => 'test_nick_name',
            'avatar' => 'test_avatar',
            'province' => 'test_province',
            'city' => 'test_city',
            'gender' => AlipayUserGender::MALE->value,
        ]);

        $this->assertEquals('test_nick_name', $user->getNickName());
        $this->assertEquals('test_avatar', $user->getAvatar());
        $this->assertEquals('test_province', $user->getProvince());
        $this->assertEquals('test_city', $user->getCity());
        $this->assertEquals(AlipayUserGender::MALE, $user->getGender());
    }

    public function testUpdateUserInfoWithPartialFields(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_2');
        $miniProgram->setName('Test App 2');
        $miniProgram->setPrivateKey('test_private_key_2');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_2');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id_2');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);

        $userService->updateUserInfo($user, ['nick_name' => 'new_nick_name']);

        $this->assertEquals('new_nick_name', $user->getNickName());
        $this->assertNull($user->getAvatar());
        $this->assertNull($user->getProvince());
        $this->assertNull($user->getCity());
        $this->assertNull($user->getGender());
    }

    public function testServiceCanBeInstantiated(): void
    {
        $userService = self::getService(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
    }

    public function testBindPhone(): void
    {
        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_bind');
        $miniProgram->setName('Test App Bind');
        $miniProgram->setPrivateKey('test_private_key_bind');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_bind');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id_bind');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);
        $phoneNumber = '13800138000';

        // 执行绑定
        $userService->bindPhone($user, $phoneNumber);

        // 验证绑定成功
        $phoneRepository = self::getService(PhoneRepository::class);
        $phone = $phoneRepository->findByNumber($phoneNumber);

        $this->assertNotNull($phone);
        $this->assertEquals($phoneNumber, $phone->getNumber());

        // 验证关联关系
        $userPhones = $user->getUserPhones();
        $this->assertCount(1, $userPhones);

        $firstUserPhone = $userPhones->first();
        $this->assertNotFalse($firstUserPhone, 'Should have at least one user phone');

        $phone = $firstUserPhone->getPhone();
        $this->assertNotNull($phone, 'User phone should have associated phone');
        $this->assertEquals($phoneNumber, $phone->getNumber());
    }

    public function testBindPhoneWithExistingPhone(): void
    {
        // 先创建一个已存在的手机号
        $existingPhone = new Phone();
        $existingPhone->setNumber('13900139000');
        self::getService(EntityManagerInterface::class)->persist($existingPhone);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_existing');
        $miniProgram->setName('Test App Existing');
        $miniProgram->setPrivateKey('test_private_key_existing');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_existing');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id_existing');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);

        // 绑定已存在的手机号
        $userService->bindPhone($user, '13900139000');

        // 验证只有一个手机号记录
        $phoneRepository = self::getService(PhoneRepository::class);
        $phones = $phoneRepository->findBy(['number' => '13900139000']);
        $this->assertCount(1, $phones);
    }

    public function testGetLatestPhone(): void
    {
        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_phone');
        $miniProgram->setName('Test App Phone');
        $miniProgram->setPrivateKey('test_private_key_phone');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_phone');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id_phone');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);

        // 测试无手机号情况
        $phone = $userService->getLatestPhone($user);
        $this->assertNull($phone);

        // 绑定手机号后测试
        $userService->bindPhone($user, '13800138001');
        $phone = $userService->getLatestPhone($user);
        $this->assertEquals('13800138001', $phone);
    }

    public function testGetAllPhones(): void
    {
        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_all_phones');
        $miniProgram->setName('Test App All Phones');
        $miniProgram->setPrivateKey('test_private_key_all_phones');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_all_phones');
        self::getService(EntityManagerInterface::class)->persist($miniProgram);

        $user = new User();
        $user->setOpenId('test_open_id_all_phones');
        $user->setMiniProgram($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $userService = self::getService(UserService::class);

        // 测试无手机号情况
        $phones = $userService->getAllPhones($user);
        $this->assertIsArray($phones);
        $this->assertEmpty($phones);

        // 绑定多个手机号后测试
        $userService->bindPhone($user, '13800138002');
        $userService->bindPhone($user, '13900139002');

        $phones = $userService->getAllPhones($user);
        $this->assertIsArray($phones);
        $this->assertCount(2, $phones);
        $this->assertContains('13800138002', $phones);
        $this->assertContains('13900139002', $phones);
    }

    // InterfaceStub方法 - 简化测试中的接口实现

    /**
     * 创建UserInterface的简单stub实现
     *
     * @param non-empty-string $userIdentifier 用户标识符
     * @param array<string> $roles 用户角色数组
     */
    private function createUserStub(string $userIdentifier = 'test-user', array $roles = []): UserInterface
    {
        // @phpstan-ignore-next-line PreferInterfaceStubTraitRule.createTestUser
        return new class($userIdentifier, $roles) implements UserInterface {
            /**
             * @param non-empty-string $userIdentifier
             * @param array<string> $roles
             */
            public function __construct(
                private string $userIdentifier,
                private array $roles,
            ) {
            }

            /** @return non-empty-string */
            public function getUserIdentifier(): string
            {
                return $this->userIdentifier;
            }

            public function getRoles(): array
            {
                return $this->roles;
            }

            public function eraseCredentials(): void
            {
                // 空实现 - stub不需要真正的凭据管理
            }
        };
    }

    public function testRequireAlipayUserException(): void
    {
        // @phpstan-ignore-next-line PreferInterfaceStubTraitRule.createTestUser
        $bizUser = $this->createUserStub('nonexistent_open_id');

        $userService = self::getService(UserService::class);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('未找到对应的支付宝用户');

        $userService->requireAlipayUser($bizUser);
    }
}
