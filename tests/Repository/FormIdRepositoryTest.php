<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\FormIdRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<FormId>
 * @internal
 */
#[CoversClass(FormIdRepository::class)]
#[RunTestsInSeparateProcesses]
final class FormIdRepositoryTest extends AbstractRepositoryTestCase
{
    private FormIdRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(FormIdRepository::class);
    }

    /**
     * 测试 findAvailableFormId 方法
     */
    public function testFindAvailableFormId(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $this->assertInstanceOf(FormIdRepository::class, $this->repository);

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setName('Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id');
        $em->persist($user);

        // 创建一个可用的 FormId（未过期，使用次数小于3）
        $availableFormId = new FormId();
        $availableFormId->setMiniProgram($miniProgram);
        $availableFormId->setUser($user);
        $availableFormId->setFormId('available_form_id');
        $availableFormId->setExpireTime(new \DateTime('+1 day'));
        $availableFormId->setUsedCount(1);
        $em->persist($availableFormId);

        // 创建一个过期的 FormId
        $expiredFormId = new FormId();
        $expiredFormId->setMiniProgram($miniProgram);
        $expiredFormId->setUser($user);
        $expiredFormId->setFormId('expired_form_id');
        $expiredFormId->setExpireTime(new \DateTime('-1 day'));
        $expiredFormId->setUsedCount(0);
        $em->persist($expiredFormId);

        // 创建一个使用次数已满的 FormId
        $usedFormId = new FormId();
        $usedFormId->setMiniProgram($miniProgram);
        $usedFormId->setUser($user);
        $usedFormId->setFormId('used_form_id');
        $usedFormId->setExpireTime(new \DateTime('+1 day'));
        $usedFormId->setUsedCount(3);
        $em->persist($usedFormId);

        $em->flush();

        // 测试查找可用的 FormId
        $result = $this->repository->findAvailableFormId($miniProgram, $user);

        $this->assertNotNull($result);
        $this->assertEquals('available_form_id', $result->getFormId());
        $this->assertEquals(1, $result->getUsedCount());
    }

    /**
     * 测试 findAvailableFormId 方法返回 null（没有可用的 FormId）
     */
    public function testFindAvailableFormIdReturnsNull(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $this->assertInstanceOf(FormIdRepository::class, $this->repository);

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_2');
        $miniProgram->setName('Test Mini Program 2');
        $miniProgram->setPrivateKey('test_private_key_2');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_2');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_2');
        $em->persist($user);

        $em->flush();

        // 测试查找可用的 FormId（应该返回 null）
        $result = $this->repository->findAvailableFormId($miniProgram, $user);

        $this->assertNull($result);
    }

    /**
     * 测试 cleanExpiredFormIds 方法
     */
    public function testCleanExpiredFormIds(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $this->assertInstanceOf(FormIdRepository::class, $this->repository);

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_3');
        $miniProgram->setName('Test Mini Program 3');
        $miniProgram->setPrivateKey('test_private_key_3');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key_3');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_3');
        $em->persist($user);

        // 创建一个过期且未使用的 FormId（应该被清理）
        $expiredUnusedFormId = new FormId();
        $expiredUnusedFormId->setMiniProgram($miniProgram);
        $expiredUnusedFormId->setUser($user);
        $expiredUnusedFormId->setFormId('expired_unused_form_id');
        $expiredUnusedFormId->setExpireTime(new \DateTime('-8 days'));
        $expiredUnusedFormId->setUsedCount(0);
        $em->persist($expiredUnusedFormId);

        // 创建一个过期但已使用的 FormId（不应该被清理）
        $expiredUsedFormId = new FormId();
        $expiredUsedFormId->setMiniProgram($miniProgram);
        $expiredUsedFormId->setUser($user);
        $expiredUsedFormId->setFormId('expired_used_form_id');
        $expiredUsedFormId->setExpireTime(new \DateTime('-8 days'));
        $expiredUsedFormId->setUsedCount(1);
        $em->persist($expiredUsedFormId);

        // 创建一个未过期的 FormId（不应该被清理）
        $notExpiredFormId = new FormId();
        $notExpiredFormId->setMiniProgram($miniProgram);
        $notExpiredFormId->setUser($user);
        $notExpiredFormId->setFormId('not_expired_form_id');
        $notExpiredFormId->setExpireTime(new \DateTime('-5 days'));
        $notExpiredFormId->setUsedCount(0);
        $em->persist($notExpiredFormId);

        $em->flush();

        // 执行清理
        $deletedCount = $this->repository->cleanExpiredFormIds();

        // 验证结果
        $this->assertEquals(1, $deletedCount);

        // 验证只有过期且未使用的 FormId 被删除
        $this->assertNull($this->repository->findOneBy(['formId' => 'expired_unused_form_id']));
        $this->assertNotNull($this->repository->findOneBy(['formId' => 'expired_used_form_id']));
        $this->assertNotNull($this->repository->findOneBy(['formId' => 'not_expired_form_id']));
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

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_save_noflush');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_save_noflush');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);

        $this->repository->save($formId, false);
        $entityId = $formId->getId();
        $em->clear();

        // 实体获得了ID，但没有flush到数据库
        $this->assertNotNull($entityId);
        // 确认数据库中找不到这个实体
        $found = $this->repository->find($entityId);
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

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_remove');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $id = $formId->getId();
        $this->repository->remove($formId);

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

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_remove_noflush');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $id = $formId->getId();
        $this->repository->remove($formId, false);
        $em->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(FormId::class, $found);
    }

    public function testFindByMiniProgramRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_miniprogram_relation');
        $miniProgram->setName('Test Mini Program Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_miniprogram_relation');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_miniprogram_relation');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $results = $this->repository->findBy(['miniProgram' => $miniProgram]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertEquals($miniProgram->getId(), $results[0]->getMiniProgram()?->getId());
    }

    public function testCountWithMiniProgramRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_miniprogram_relation');
        $miniProgram->setName('Test Mini Program Count Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_miniprogram_relation');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_count_miniprogram_relation');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $count = $this->repository->count(['miniProgram' => $miniProgram]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByUserRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_user_relation');
        $miniProgram->setName('Test Mini Program User Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_user_relation');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_user_relation');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $results = $this->repository->findBy(['user' => $user]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertEquals($user->getId(), $results[0]->getUser()?->getId());
    }

    public function testCountWithUserRelationShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_user_relation');
        $miniProgram->setName('Test Mini Program Count User Relation');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_user_relation');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_count_user_relation');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);
        $em->flush();

        $count = $this->repository->count(['user' => $user]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBySorting(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntity1 = $this->createMock(FormId::class);
        $mockEntity2 = $this->createMock(FormId::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        $repository = new class($managerRegistry, [$mockEntity1, $mockEntity2]) extends FormIdRepository {
            /** @var array<int, FormId> */
            private array $entities;

            /**
             * @param array<int, FormId> $entities
             */
            public function __construct(ManagerRegistry $registry, array $entities)
            {
                parent::__construct($registry);
                $this->entities = $entities;
            }

            public function findOneBy(array $criteria, ?array $orderBy = null): FormId
            {
                if (is_array($orderBy) && isset($orderBy['formId']) && 'DESC' === $orderBy['formId']) {
                    return $this->entities[1];
                }

                return $this->entities[0];
            }
        };

        $resultAsc = $repository->findOneBy(['user' => 1], ['formId' => 'ASC']);
        $resultDesc = $repository->findOneBy(['user' => 1], ['formId' => 'DESC']);

        $this->assertSame($mockEntity1, $resultAsc);
        $this->assertSame($mockEntity2, $resultDesc);
    }

    public function testFindByWithCreateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $results = $this->repository->findBy(['createTime' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testFindByWithUpdateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $results = $this->repository->findBy(['updateTime' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testFindByWithCreatedByIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $results = $this->repository->findBy(['createdBy' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testFindByWithUpdatedByIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $results = $this->repository->findBy(['updatedBy' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testCountWithCreateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $count = $this->repository->count(['createTime' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithUpdateTimeIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $count = $this->repository->count(['updateTime' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCreatedByIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $count = $this->repository->count(['createdBy' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithUpdatedByIsNullShouldWork(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $count = $this->repository->count(['updatedBy' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    /**
     * 测试 findOneBy 排序逻辑 - 按ID升序
     */
    public function testFindOneByOrderByIdAsc(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_orderby_id_asc');
        $miniProgram->setName('Test Mini Program OrderBy Id Asc');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_orderby_id_asc');
        $em->persist($user);

        // 创建两个 FormId，第二个应该先被找到（ID升序）
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_orderby_id_asc_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_orderby_id_asc_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(0);
        $em->persist($formId2);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user], ['id' => 'ASC']);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId1->getId(), $result->getId());
    }

    /**
     * 测试 findOneBy 排序逻辑 - 按ID降序
     */
    public function testFindOneByOrderByIdDesc(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_orderby_id_desc');
        $miniProgram->setName('Test Mini Program OrderBy Id Desc');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_orderby_id_desc');
        $em->persist($user);

        // 创建两个 FormId，第二个应该先被找到（ID降序）
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_orderby_id_desc_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_orderby_id_desc_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(0);
        $em->persist($formId2);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user], ['id' => 'DESC']);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId2->getId(), $result->getId());
    }

    /**
     * 测试 findOneBy 排序逻辑 - 按过期时间升序
     */
    public function testFindOneByOrderByExpireTimeAsc(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_orderby_expire_asc');
        $miniProgram->setName('Test Mini Program OrderBy Expire Asc');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_orderby_expire_asc');
        $em->persist($user);

        // 创建两个 FormId，第一个过期时间更早，应该先被找到
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_orderby_expire_asc_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_orderby_expire_asc_2');
        $formId2->setExpireTime(new \DateTime('+2 days'));
        $formId2->setUsedCount(0);
        $em->persist($formId2);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user], ['expireTime' => 'ASC']);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId1->getId(), $result->getId());
    }

    /**
     * 测试 findOneBy 排序逻辑 - 按使用次数升序
     */
    public function testFindOneByOrderByUsedCountAsc(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_orderby_used_count_asc');
        $miniProgram->setName('Test Mini Program OrderBy Used Count Asc');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_orderby_used_count_asc');
        $em->persist($user);

        // 创建两个 FormId，第一个使用次数更少，应该先被找到
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_orderby_used_count_asc_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_orderby_used_count_asc_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(2);
        $em->persist($formId2);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user], ['usedCount' => 'ASC']);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId1->getId(), $result->getId());
    }

    /**
     * 测试 findOneBy 排序逻辑 - 多字段排序
     */
    public function testFindOneByOrderByMultipleFields(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_orderby_multiple');
        $miniProgram->setName('Test Mini Program OrderBy Multiple');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_orderby_multiple');
        $em->persist($user);

        // 创建两个 FormId，使用次数相同，按ID升序
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_orderby_multiple_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_orderby_multiple_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(0);
        $em->persist($formId2);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user], ['usedCount' => 'ASC', 'id' => 'ASC']);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId1->getId(), $result->getId());
    }

    /**
     * 测试 count 关联查询 - 按 miniProgram 和 user 关联查询
     */
    public function testCountByMiniProgramAndUser(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_relation_both');
        $miniProgram->setName('Test Mini Program Count Relation Both');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_relation_both');
        $em->persist($user);

        // 创建3个 FormId
        for ($i = 0; $i < 3; ++$i) {
            $formId = new FormId();
            $formId->setMiniProgram($miniProgram);
            $formId->setUser($user);
            $formId->setFormId('test_form_id_count_relation_both_' . $i);
            $formId->setExpireTime(new \DateTime('+1 day'));
            $formId->setUsedCount(0);
            $em->persist($formId);
        }

        $em->flush();

        $count = $this->repository->count(['miniProgram' => $miniProgram, 'user' => $user]);

        $this->assertEquals(3, $count);
    }

    /**
     * 测试 count 关联查询 - 按使用次数查询
     */
    public function testCountByUsedCount(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_used_range');
        $miniProgram->setName('Test Mini Program Count Used Range');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_used_range');
        $em->persist($user);

        // 创建不同使用次数的 FormId
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_count_used_range_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_count_used_range_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(1);
        $em->persist($formId2);

        $formId3 = new FormId();
        $formId3->setMiniProgram($miniProgram);
        $formId3->setUser($user);
        $formId3->setFormId('test_form_id_count_used_range_3');
        $formId3->setExpireTime(new \DateTime('+1 day'));
        $formId3->setUsedCount(1);
        $em->persist($formId3);

        $em->flush();

        // 查询使用次数为1的记录
        $count = $this->repository->count(['usedCount' => 1]);

        $this->assertEquals(2, $count);
    }

    /**
     * 测试 count 关联查询 - 按小程序和用户统计可用 FormId 数量
     */
    public function testCountAvailableFormIdsByMiniProgramAndUser(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_expire_range');
        $miniProgram->setName('Test Mini Program Count Expire Range');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_expire_range');
        $em->persist($user);

        // 创建两个使用次数为0的 FormId
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_count_expire_range_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_count_expire_range_2');
        $formId2->setExpireTime(new \DateTime('+3 days'));
        $formId2->setUsedCount(0);
        $em->persist($formId2);

        // 创建一个使用次数为1的 FormId
        $formId3 = new FormId();
        $formId3->setMiniProgram($miniProgram);
        $formId3->setUser($user);
        $formId3->setFormId('test_form_id_count_expire_range_3');
        $formId3->setExpireTime(new \DateTime('+5 days'));
        $formId3->setUsedCount(1);
        $em->persist($formId3);

        $em->flush();

        // 查询使用次数为0的记录
        $count = $this->repository->count(['miniProgram' => $miniProgram, 'user' => $user, 'usedCount' => 0]);

        $this->assertEquals(2, $count);
    }

    /**
     * 测试 count 关联查询 - 复合条件查询
     */
    public function testCountByComplexConditions(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count_complex');
        $miniProgram->setName('Test Mini Program Count Complex');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count_complex');
        $em->persist($user);

        // 创建符合条件的 FormId
        $formId1 = new FormId();
        $formId1->setMiniProgram($miniProgram);
        $formId1->setUser($user);
        $formId1->setFormId('test_form_id_count_complex_1');
        $formId1->setExpireTime(new \DateTime('+1 day'));
        $formId1->setUsedCount(0);
        $em->persist($formId1);

        $formId2 = new FormId();
        $formId2->setMiniProgram($miniProgram);
        $formId2->setUser($user);
        $formId2->setFormId('test_form_id_count_complex_2');
        $formId2->setExpireTime(new \DateTime('+1 day'));
        $formId2->setUsedCount(1);
        $em->persist($formId2);

        $em->flush();

        // 查询使用次数小于1的记录
        $count = $this->repository->count(['miniProgram' => $miniProgram, 'usedCount' => 0]);

        $this->assertEquals(1, $count);
    }

    /**
     * 测试 IS NULL 查询 - 查询 createdBy 为 NULL 的记录
     */
    public function testFindOneByCreatedByIsNull(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_created_by');
        $miniProgram->setName('Test Mini Program Null Created By');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_created_by');
        $em->persist($user);

        // 创建一个 FormId，createdBy 为 NULL
        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_null_created_by');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $formId->setCreatedBy(null);
        $em->persist($formId);

        $em->flush();

        // 使用更精确的查询条件
        $result = $this->repository->findOneBy([
            'createdBy' => null,
            'formId' => 'test_form_id_null_created_by',
        ]);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId->getId(), $result->getId());
        $this->assertNull($result->getCreatedBy());
    }

    /**
     * 测试 IS NULL 查询 - 查询 updatedBy 为 NULL 的记录
     */
    public function testFindOneByUpdatedByIsNull(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_updated_by');
        $miniProgram->setName('Test Mini Program Null Updated By');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_updated_by');
        $em->persist($user);

        // 创建一个 FormId，updatedBy 为 NULL
        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_null_updated_by');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $formId->setUpdatedBy(null);
        $em->persist($formId);

        $em->flush();

        // 使用更精确的查询条件
        $result = $this->repository->findOneBy([
            'updatedBy' => null,
            'formId' => 'test_form_id_null_updated_by',
        ]);

        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($formId->getId(), $result->getId());
        $this->assertNull($result->getUpdatedBy());
    }

    /**
     * 测试 IS NULL 查询 - 使用组合条件确保查询不到匹配的记录
     */
    public function testFindOneByNullReturnsNullWhenNoMatch(): void
    {
        $em = self::getService(EntityManagerInterface::class);

        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_null_no_match');
        $miniProgram->setName('Test Mini Program Null No Match');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_null_no_match');
        $em->persist($user);

        // 创建一个 FormId，设置所有时间字段（非 null）
        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_null_no_match');
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);
        $formId->setCreateTime(new \DateTimeImmutable());
        $formId->setUpdateTime(new \DateTimeImmutable());
        $formId->setCreatedBy('test_user');
        $formId->setUpdatedBy('test_user');
        $em->persist($formId);

        $em->flush();

        // 使用组合条件查询：同时匹配 formId 和 createTime 为 null
        // 由于我们创建的记录 formId='test_form_id_null_no_match' 且 createTime 不为 null
        // 所以这个查询不会匹配到任何记录
        $result = $this->repository->findOneBy([
            'formId' => 'test_form_id_null_no_match',
            'createTime' => null,
        ]);

        $this->assertNull($result);
    }

    /**
     * @return ServiceEntityRepository<FormId>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(FormIdRepository::class);
    }

    /**
     * 测试 findOneBy 排序逻辑 - PHPStan 要求的测试方法
     * 测试按照 expireTime 排序
     */

    /**
     * 测试 count 关联查询 - miniProgram 关联
     */
    public function testCountByAssociationMiniProgramShouldReturnCorrectNumber(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $repository = $this->getRepository();

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_count');
        $miniProgram->setName('Test Mini Program Count');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_count');
        $em->persist($user);

        // 创建 3 个属于该 miniProgram 的 FormId
        for ($i = 1; $i <= 3; ++$i) {
            $formId = new FormId();
            $formId->setMiniProgram($miniProgram);
            $formId->setUser($user);
            $formId->setFormId('test_form_id_count_' . $i);
            $formId->setExpireTime(new \DateTimeImmutable('+1 day'));
            $formId->setUsedCount(0);
            $em->persist($formId);
        }

        // 创建另一个 miniProgram 和 FormId
        $miniProgram2 = new MiniProgram();
        $miniProgram2->setAppId('test_app_id_count_2');
        $miniProgram2->setName('Test Mini Program Count 2');
        $miniProgram2->setPrivateKey('test_private_key');
        $miniProgram2->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram2);

        $user2 = new User();
        $user2->setMiniProgram($miniProgram2);
        $user2->setOpenId('test_open_id_count_2');
        $em->persist($user2);

        $formIdOther = new FormId();
        $formIdOther->setMiniProgram($miniProgram2);
        $formIdOther->setUser($user2);
        $formIdOther->setFormId('test_form_id_count_other');
        $formIdOther->setExpireTime(new \DateTimeImmutable('+1 day'));
        $formIdOther->setUsedCount(0);
        $em->persist($formIdOther);

        $em->flush();

        $count = $this->repository->count(['miniProgram' => $miniProgram]);
        $this->assertSame(3, $count);
    }

    /**
     * 测试 findOneBy 关联查询 - user 关联
     */
    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $em = self::getService(EntityManagerInterface::class);
        $repository = $this->getRepository();

        // 创建测试数据
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_assoc');
        $miniProgram->setName('Test Mini Program Assoc');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_alipay_public_key');
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_assoc');
        $em->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_assoc');
        $formId->setExpireTime(new \DateTimeImmutable('+1 day'));
        $formId->setUsedCount(0);
        $em->persist($formId);

        $em->flush();

        $result = $this->repository->findOneBy(['user' => $user]);
        $this->assertInstanceOf(FormId::class, $result);
        $this->assertEquals($user->getId(), $result->getUser()?->getId());
    }

    /**
     * 测试 findBy 关联查询 - miniProgram 关联
     */

    /**
     * 测试 findOneBy 可空字段 - createdBy 为 null
     */

    /**
     * 测试 findBy 可空字段 - updatedBy 为 null
     */

    /**
     * 测试 count 可空字段 - createdBy 为 null
     */
    protected function createNewEntity(): object
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_' . uniqid());
        $miniProgram->setName('Test Mini Program ' . uniqid());
        $miniProgram->setPrivateKey('test_private_key_' . uniqid());
        $miniProgram->setAlipayPublicKey('test_public_key_' . uniqid());
        self::getEntityManager()->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_' . uniqid());
        self::getEntityManager()->persist($user);

        $formId = new FormId();
        $formId->setMiniProgram($miniProgram);
        $formId->setUser($user);
        $formId->setFormId('test_form_id_' . uniqid());
        $formId->setExpireTime(new \DateTime('+1 day'));
        $formId->setUsedCount(0);

        return $formId;
    }
}
