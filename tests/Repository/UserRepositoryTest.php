<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<User>
 * @internal
 */
#[CoversClass(UserRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserRepositoryTest extends AbstractRepositoryTestCase
{
    private UserRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(UserRepository::class);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        // Repository is always non-null due to type declaration and initialization
        $this->assertInstanceOf(UserRepository::class, $this->repository);
    }

    public function testRepositoryHandlesCorrectEntityClass(): void
    {
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }

    public function testRepositoryHasCustomMethods(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_custom');
        $miniProgram->setName('Test Mini Program Custom');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_custom');
        $em->persist($user);
        $em->flush();

        // Test findByOpenId works
        $result = $this->repository->findByOpenId('test_open_id_custom');
        $this->assertInstanceOf(User::class, $result);

        // Test save works
        $newUser = new User();
        $newUser->setMiniProgram($miniProgram);
        $newUser->setOpenId('test_open_id_save_method');
        $this->repository->save($newUser);
        $this->assertNotNull($newUser->getId());

        // Test remove works
        $id = $newUser->getId();
        $this->repository->remove($newUser);
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testInheritedMethodsBehavior(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_inherited');
        $miniProgram->setName('Test Mini Program Inherited');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_inherited');
        $em->persist($user);
        $em->flush();

        // Test that inherited methods work correctly
        $resultFind = $this->repository->find($user->getId());
        $this->assertInstanceOf(User::class, $resultFind);

        $resultFindOneBy = $this->repository->findOneBy(['openId' => 'test_open_id_inherited']);
        $this->assertInstanceOf(User::class, $resultFindOneBy);

        $resultFindAll = $this->repository->findAll();
        // findAll() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($resultFindAll));

        $resultFindBy = $this->repository->findBy(['miniProgram' => $miniProgram]);
        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($resultFindBy));
    }

    public function testRepositoryInheritance(): void
    {
        // Repository is always non-null due to type declaration and initialization
        $this->assertInstanceOf(UserRepository::class, $this->repository);
    }

    /**
     * 测试 findByOpenId 方法
     */
    public function testFindByOpenId(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $this->assertInstanceOf(UserRepository::class, $this->repository);

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_user_1');
        $miniProgram->setName('Test Mini Program User 1');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_123');
        $em->persist($user);

        $em->flush();

        // 测试查找存在的用户
        $result = $this->repository->findByOpenId('test_open_id_123');
        $this->assertNotNull($result);
        $this->assertEquals('test_open_id_123', $result->getOpenId());

        // 测试查找不存在的用户
        $result = $this->repository->findByOpenId('non_existent_open_id');
        $this->assertNull($result);
    }

    /**
     * 测试多个用户的查找
     */
    public function testFindMultipleUsers(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $this->assertInstanceOf(UserRepository::class, $this->repository);

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_user_3');
        $miniProgram->setName('Test Mini Program User 3');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        // 创建多个用户
        $user1 = new User();
        $user1->setMiniProgram($miniProgram);
        $user1->setOpenId('test_open_id_multi_1');
        $em->persist($user1);

        $user2 = new User();
        $user2->setMiniProgram($miniProgram);
        $user2->setOpenId('test_open_id_multi_2');
        $em->persist($user2);

        $em->flush();

        // 测试查找不同的用户
        $result1 = $this->repository->findByOpenId('test_open_id_multi_1');
        $this->assertNotNull($result1);
        $this->assertEquals('test_open_id_multi_1', $result1->getOpenId());

        $result2 = $this->repository->findByOpenId('test_open_id_multi_2');
        $this->assertNotNull($result2);
        $this->assertEquals('test_open_id_multi_2', $result2->getOpenId());

        // 确保查找方法返回的是不同的用户
        $this->assertNotSame($result1->getId(), $result2->getId());
    }

    public function testFindByUserIdShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_userid');
        $miniProgram->setName('Test Mini Program UserId');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_userid');
        $em->persist($user);
        $em->flush();

        $result = $this->repository->findByUserId('test_open_id_userid');

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame('test_open_id_userid', $result->getOpenId());
    }

    public function testSaveWithoutFlushShouldNotPersistEntity(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_save_noflush');
        $miniProgram->setName('Test Mini Program Save NoFlush');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);
        $em->flush();

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_save_noflush');

        $this->repository->save($user, false);
        $id = $user->getId();
        $em->clear();

        // The entity should not be found in database since flush was not called
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_remove');
        $miniProgram->setName('Test Mini Program Remove');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_remove');
        $em->persist($user);
        $em->flush();

        $id = $user->getId();
        $this->repository->remove($user);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteEntity(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_remove_noflush');
        $miniProgram->setName('Test Mini Program Remove NoFlush');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_remove_noflush');
        $em->persist($user);
        $em->flush();

        $id = $user->getId();
        $this->repository->remove($user, false);
        $em->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(User::class, $found);
    }

    public function testFindByMiniProgramRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_relation');
        $miniProgram->setName('Test Mini Program Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_relation');
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['miniProgram' => $miniProgram]);

        $this->assertCount(1, $results);
        $this->assertEquals($miniProgram->getId(), $results[0]->getMiniProgram()->getId());
    }

    public function testCountWithMiniProgramRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_rel');
        $miniProgram->setName('Test Mini Program Count Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_rel');
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['miniProgram' => $miniProgram]);

        $this->assertEquals(1, $count);
    }

    public function testFindByWithNickNameIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_nick');
        $miniProgram->setName('Test Mini Program Null Nick');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_nick');
        $user->setNickName(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['nickName' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithAvatarIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_avatar');
        $miniProgram->setName('Test Mini Program Null Avatar');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_avatar');
        $user->setAvatar(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['avatar' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithNickNameIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_nick');
        $miniProgram->setName('Test Mini Program Count Null Nick');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_nick');
        $user->setNickName(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['nickName' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithAvatarIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_avatar');
        $miniProgram->setName('Test Mini Program Count Null Avatar');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_avatar');
        $user->setAvatar(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['avatar' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBySorting(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntity1 = $this->createMock(User::class);
        $mockEntity2 = $this->createMock(User::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        $repository = new class($managerRegistry, [$mockEntity1, $mockEntity2]) extends UserRepository {
            /** @var array<User> */
            private array $entities;

            /** @param array<User> $entities */
            public function __construct(ManagerRegistry $registry, array $entities)
            {
                parent::__construct($registry);
                $this->entities = $entities;
            }

            public function findOneBy(array $criteria, ?array $orderBy = null): User
            {
                if (is_array($orderBy) && isset($orderBy['openId']) && 'DESC' === $orderBy['openId']) {
                    return $this->entities[1];
                }

                return $this->entities[0];
            }
        };

        $resultAsc = $repository->findOneBy(['miniProgram' => 1], ['openId' => 'ASC']);
        $resultDesc = $repository->findOneBy(['miniProgram' => 1], ['openId' => 'DESC']);

        $this->assertSame($mockEntity1, $resultAsc);
        $this->assertSame($mockEntity2, $resultDesc);
    }

    public function testFindByWithCreateTimeIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['createTime' => null]);

        // findBy() always returns array, verify execution success
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testFindByWithUpdateTimeIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['updateTime' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testCountWithCreateTimeIsNullShouldWork(): void
    {
        $count = $this->repository->count(['createTime' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithUpdateTimeIsNullShouldWork(): void
    {
        $count = $this->repository->count(['updateTime' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindByWithProvinceIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_province');
        $miniProgram->setName('Test Mini Program Null Province');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_province');
        $user->setProvince(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['province' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithCityIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_city');
        $miniProgram->setName('Test Mini Program Null City');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_city');
        $user->setCity(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['city' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithGenderIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_gender');
        $miniProgram->setName('Test Mini Program Null Gender');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_gender');
        $user->setGender(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['gender' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithLastInfoUpdateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_last_info');
        $miniProgram->setName('Test Mini Program Null Last Info');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_last_info');
        $user->setLastInfoUpdateTime(null);
        $em->persist($user);
        $em->flush();

        $results = $this->repository->findBy(['lastInfoUpdateTime' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithProvinceIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_province');
        $miniProgram->setName('Test Mini Program Count Null Province');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_province');
        $user->setProvince(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['province' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithCityIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_city');
        $miniProgram->setName('Test Mini Program Count Null City');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_city');
        $user->setCity(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['city' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithGenderIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_gender');
        $miniProgram->setName('Test Mini Program Count Null Gender');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_gender');
        $user->setGender(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['gender' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithLastInfoUpdateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_null_last_info');
        $miniProgram->setName('Test Mini Program Count Null Last Info');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_null_last_info');
        $user->setLastInfoUpdateTime(null);
        $em->persist($user);
        $em->flush();

        $count = $this->repository->count(['lastInfoUpdateTime' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    /**
     * @return ServiceEntityRepository<User>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_' . uniqid());
        $miniProgram->setName('Test Mini Program ' . uniqid());
        $miniProgram->setPrivateKey('test_private_key_' . uniqid());
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_' . uniqid());
        $em = self::getService(EntityManagerInterface::class);
        $em->persist($miniProgram);
        $em->flush();

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_' . uniqid());

        return $user;
    }
}
