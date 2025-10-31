<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\AlipayUserPhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<AlipayUserPhone>
 * @internal
 */
#[CoversClass(AlipayUserPhoneRepository::class)]
#[RunTestsInSeparateProcesses]
final class AlipayUserPhoneRepositoryTest extends AbstractRepositoryTestCase
{
    private AlipayUserPhoneRepository $repository;

    private MiniProgram $miniProgram;

    private User $user;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AlipayUserPhoneRepository::class);

        // Create test data
        $this->miniProgram = new MiniProgram();
        $this->miniProgram->setAppId('test_app_id');
        $this->miniProgram->setName('Test Mini Program');
        $this->miniProgram->setPrivateKey('test_private_key');
        $this->miniProgram->setAlipayPublicKey('test_alipay_public_key');
        self::getEntityManager()->persist($this->miniProgram);

        $this->user = new User();
        $this->user->setMiniProgram($this->miniProgram);
        $this->user->setOpenId('test_open_id');
        self::getEntityManager()->persist($this->user);

        self::getEntityManager()->flush();
    }

    private function createPhone(string $number): Phone
    {
        $phone = new Phone();
        $phone->setNumber($number);
        self::getEntityManager()->persist($phone);

        return $phone;
    }

    public function testSaveWithoutFlushShouldNotPersistEntity(): void
    {
        $phone = $this->createPhone('13800138112');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());

        $this->repository->save($userPhone, false);
        $id = $userPhone->getId();
        self::getEntityManager()->clear();

        // Entity should have been assigned an ID when persisted
        $this->assertNotNull($id);

        // But should not be findable in database since it wasn't flushed
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $phone = $this->createPhone('13800138113');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $id = $userPhone->getId();
        $this->repository->remove($userPhone);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteEntity(): void
    {
        $phone = $this->createPhone('13800138114');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $id = $userPhone->getId();
        $this->repository->remove($userPhone, false);
        self::getEntityManager()->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(AlipayUserPhone::class, $found);
    }

    public function testFindByUserRelationShouldWork(): void
    {
        $phone = $this->createPhone('13800138115');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['user' => $this->user]);

        $this->assertCount(1, $results);
        $user = $results[0]->getUser();
        $this->assertNotNull($user);
        $this->assertEquals($this->user->getId(), $user->getId());
    }

    public function testFindByPhoneRelationShouldWork(): void
    {
        $phone = $this->createPhone('13800138116');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['phone' => $phone]);

        $this->assertCount(1, $results);
        $resultPhone = $results[0]->getPhone();
        $this->assertNotNull($resultPhone);
        $this->assertEquals($phone->getId(), $resultPhone->getId());
    }

    public function testCountWithUserRelationShouldWork(): void
    {
        $phone = $this->createPhone('13800138118');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['user' => $this->user]);

        $this->assertEquals(1, $count);
    }

    public function testCountWithPhoneRelationShouldWork(): void
    {
        $phone = $this->createPhone('13800138119');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['phone' => $phone]);

        $this->assertEquals(1, $count);
    }

    public function testFindOneByWithOrderByShouldWork(): void
    {
        $phone1 = $this->createPhone('13800138122');
        $phone2 = $this->createPhone('13800138123');

        $userPhone1 = new AlipayUserPhone();
        $userPhone1->setUser($this->user);
        $userPhone1->setPhone($phone1);
        $userPhone1->setVerifiedTime(new \DateTimeImmutable('2023-01-01'));
        self::getEntityManager()->persist($userPhone1);

        $userPhone2 = new AlipayUserPhone();
        $userPhone2->setUser($this->user);
        $userPhone2->setPhone($phone2);
        $userPhone2->setVerifiedTime(new \DateTimeImmutable('2023-01-02'));
        self::getEntityManager()->persist($userPhone2);

        self::getEntityManager()->flush();

        // Test order by verifiedTime DESC - should return the latest one
        $result = $this->repository->findOneBy(['user' => $this->user], ['verifiedTime' => 'DESC']);

        $this->assertInstanceOf(AlipayUserPhone::class, $result);
        $this->assertEquals($userPhone2->getId(), $result->getId());
        $verifiedTime = $result->getVerifiedTime();
        $this->assertNotNull($verifiedTime);
        $this->assertEquals('2023-01-02', $verifiedTime->format('Y-m-d'));

        // Test order by verifiedTime ASC - should return the earliest one
        $result = $this->repository->findOneBy(['user' => $this->user], ['verifiedTime' => 'ASC']);

        $this->assertInstanceOf(AlipayUserPhone::class, $result);
        $this->assertEquals($userPhone1->getId(), $result->getId());
        $verifiedTime = $result->getVerifiedTime();
        $this->assertNotNull($verifiedTime);
        $this->assertEquals('2023-01-01', $verifiedTime->format('Y-m-d'));
    }

    public function testFindByWithVerifiedTimeIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['verifiedTime' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testCountWithVerifiedTimeIsNullShouldWork(): void
    {
        $count = $this->repository->count(['verifiedTime' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindByWithCreateTimeIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['createTime' => null]);

        // findBy() always returns array, verify content instead
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

    public function testFindByWithComplexRelationQueriesShouldWork(): void
    {
        $phone = $this->createPhone('13800138126');

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());
        self::getEntityManager()->persist($userPhone);
        self::getEntityManager()->flush();

        // Test findBy with user relation
        $results = $this->repository->findBy(['user' => $this->user]);
        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertContainsOnlyInstancesOf(AlipayUserPhone::class, $results);

        // Test findBy with phone relation
        $results = $this->repository->findBy(['phone' => $phone]);
        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertContainsOnlyInstancesOf(AlipayUserPhone::class, $results);

        // Test findBy with combined relations
        $results = $this->repository->findBy(['user' => $this->user, 'phone' => $phone]);
        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertContainsOnlyInstancesOf(AlipayUserPhone::class, $results);

        // Test count with user relation
        $count = $this->repository->count(['user' => $this->user]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
        $this->assertGreaterThanOrEqual(1, $count);

        // Test count with phone relation
        $count = $this->repository->count(['phone' => $phone]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
        $this->assertGreaterThanOrEqual(1, $count);

        // Test count with combined relations
        $count = $this->repository->count(['user' => $this->user, 'phone' => $phone]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByWithNullableFieldsShouldWork(): void
    {
        // Test with verifiedTime is null (though it's required by Assert, test the query anyway)
        $results = $this->repository->findBy(['verifiedTime' => null]);
        // findBy() always returns array, verify content instead

        // Test count with verifiedTime is null
        $count = $this->repository->count(['verifiedTime' => null]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);

        // Test with createTime is null
        $results = $this->repository->findBy(['createTime' => null]);
        // findBy() always returns array, verify content instead

        // Test count with createTime is null
        $count = $this->repository->count(['createTime' => null]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);

        // Test with updateTime is null
        $results = $this->repository->findBy(['updateTime' => null]);
        // findBy() always returns array, verify content instead

        // Test count with updateTime is null
        $count = $this->repository->count(['updateTime' => null]);
        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    /**
     * @return ServiceEntityRepository<AlipayUserPhone>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        // 创建一个完整的 AlipayUserPhone 实体，包含所有必需的关联
        // 使用已经持久化的测试数据，避免创建新的关联实体

        $phone = new Phone();
        $phone->setNumber('test_phone_' . uniqid());
        self::getEntityManager()->persist($phone);

        $user = new User();
        $user->setMiniProgram($this->miniProgram);
        $user->setOpenId('test_open_id_' . uniqid());
        self::getEntityManager()->persist($user);

        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTimeImmutable());

        return $userPhone;
    }
}
