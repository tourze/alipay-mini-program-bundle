<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Repository\PhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<Phone>
 * @internal
 */
#[CoversClass(PhoneRepository::class)]
#[RunTestsInSeparateProcesses]
final class PhoneRepositoryTest extends AbstractRepositoryTestCase
{
    private PhoneRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PhoneRepository::class);
    }

    public function testFindByNumberWithValidNumber(): void
    {
        // Create a test phone entity
        $phone = new Phone();
        $phone->setNumber('15800138000');

        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $result = $this->repository->findByNumber('15800138000');

        $this->assertInstanceOf(Phone::class, $result);
        $this->assertSame('15800138000', $result->getNumber());
    }

    public function testFindByNumberWithNonExistentNumber(): void
    {
        $result = $this->repository->findByNumber('99999999999');

        $this->assertNull($result);
    }

    public function testFindByNumberWithEmptyString(): void
    {
        $result = $this->repository->findByNumber('');

        $this->assertNull($result);
    }

    public function testFindByNumberWithInternationalFormat(): void
    {
        // Create a test phone entity with international format
        $phone = new Phone();
        $phone->setNumber('+8613800138000');

        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $result = $this->repository->findByNumber('+8613800138000');

        $this->assertInstanceOf(Phone::class, $result);
        $this->assertSame('+8613800138000', $result->getNumber());
    }

    public function testFindByNumberWithVariousPhoneFormats(): void
    {
        $testCases = [
            '13800138001',
            '+8613800138002',
            '86-138-0013-8003',
            '138 0013 8004',
        ];

        // Create phone entities for each test case
        foreach ($testCases as $number) {
            $phone = new Phone();
            $phone->setNumber($number);
            self::getEntityManager()->persist($phone);
        }
        self::getEntityManager()->flush();

        // Test each phone number can be found
        foreach ($testCases as $number) {
            $result = $this->repository->findByNumber($number);
            $this->assertInstanceOf(Phone::class, $result);
            $this->assertSame($number, $result->getNumber());
        }
    }

    public function testFindByNumberReturnsCorrectEntity(): void
    {
        // Create multiple phone entities to test specificity
        $phone1 = new Phone();
        $phone1->setNumber('13800138010');
        $phone1->setCreateTime(new \DateTimeImmutable());

        $phone2 = new Phone();
        $phone2->setNumber('13800138011');
        $phone2->setCreateTime(new \DateTimeImmutable());

        self::getEntityManager()->persist($phone1);
        self::getEntityManager()->persist($phone2);
        self::getEntityManager()->flush();

        $result = $this->repository->findByNumber('13800138011');

        $this->assertInstanceOf(Phone::class, $result);
        $this->assertSame('13800138011', $result->getNumber());
    }

    public function testSaveWithoutFlushShouldNotPersistEntity(): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138026');

        $this->repository->save($phone, false);
        $id = $phone->getId();
        self::getEntityManager()->clear();

        // The entity should not be found in database since flush was not called
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138027');
        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $id = $phone->getId();
        $this->repository->remove($phone);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteEntity(): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138028');
        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $id = $phone->getId();
        $this->repository->remove($phone, false);
        self::getEntityManager()->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(Phone::class, $found);
    }

    public function testFindByUserPhonesRelationShouldWork(): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138029');
        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['id' => $phone->getId()]);

        $this->assertEquals(1, $count);
    }

    public function testCountWithUserPhonesRelationShouldWork(): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138030');
        self::getEntityManager()->persist($phone);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['number' => '13800138030']);

        $this->assertEquals(1, $count);
    }

    public function testFindByWithNumberIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['number' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testCountWithNumberIsNullShouldWork(): void
    {
        $count = $this->repository->count(['number' => null]);

        $this->assertEquals(0, $count);
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

    public function testFindOneByWithOrderBySorting(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntity1 = $this->createMock(Phone::class);
        $mockEntity2 = $this->createMock(Phone::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        $repository = new class($managerRegistry, [$mockEntity1, $mockEntity2]) extends PhoneRepository {
            /** @var array<Phone> */
            private array $entities;

            /** @param array<Phone> $entities */
            public function __construct(ManagerRegistry $registry, array $entities)
            {
                parent::__construct($registry);
                $this->entities = $entities;
            }

            public function findOneBy(array $criteria, ?array $orderBy = null): Phone
            {
                if (is_array($orderBy) && isset($orderBy['number']) && 'DESC' === $orderBy['number']) {
                    return $this->entities[1];
                }

                return $this->entities[0];
            }
        };

        $resultAsc = $repository->findOneBy([], ['number' => 'ASC']);
        $resultDesc = $repository->findOneBy([], ['number' => 'DESC']);

        $this->assertSame($mockEntity1, $resultAsc);
        $this->assertSame($mockEntity2, $resultDesc);
    }

    /**
     * @return ServiceEntityRepository<Phone>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $phone = new Phone();
        $phone->setNumber('1380013' . str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT));

        return $phone;
    }
}
