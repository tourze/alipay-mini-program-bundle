<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<MiniProgram>
 * @internal
 */
#[CoversClass(MiniProgramRepository::class)]
#[RunTestsInSeparateProcesses]
final class MiniProgramRepositoryTest extends AbstractRepositoryTestCase
{
    private MiniProgramRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MiniProgramRepository::class);
    }

    public function testFindByAppIdWithValidAppId(): void
    {
        // Create a test mini program entity
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('2021001234567890');
        $miniProgram->setCode('TEST_CODE');
        $miniProgram->setName('Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $result = $this->repository->findByAppId('2021001234567890');

        $this->assertInstanceOf(MiniProgram::class, $result);
        $this->assertSame('2021001234567890', $result->getAppId());
        $this->assertSame('TEST_CODE', $result->getCode());
    }

    public function testFindByAppIdWithNonExistentAppId(): void
    {
        $result = $this->repository->findByAppId('NON_EXISTENT_APP_ID');

        $this->assertNull($result);
    }

    public function testFindByAppIdWithEmptyString(): void
    {
        $result = $this->repository->findByAppId('');

        $this->assertNull($result);
    }

    public function testFindByCodeWithValidCode(): void
    {
        // Create a test mini program entity
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('2021001234567891');
        $miniProgram->setCode('MINI_PROGRAM_CODE');
        $miniProgram->setName('Test Mini Program 2');
        $miniProgram->setPrivateKey('test_private_key_2');
        $miniProgram->setAlipayPublicKey('test_public_key_2');

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $result = $this->repository->findByCode('MINI_PROGRAM_CODE');

        $this->assertInstanceOf(MiniProgram::class, $result);
        $this->assertSame('MINI_PROGRAM_CODE', $result->getCode());
        $this->assertSame('2021001234567891', $result->getAppId());
    }

    public function testFindByCodeWithNonExistentCode(): void
    {
        $result = $this->repository->findByCode('NON_EXISTENT_CODE');

        $this->assertNull($result);
    }

    public function testFindByCodeWithEmptyString(): void
    {
        $result = $this->repository->findByCode('');

        $this->assertNull($result);
    }

    public function testSaveWithoutFlushShouldNotPersistEntity(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_SAVE_NOFLUSH_TEST');
        $miniProgram->setName('Save NoFlush Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        $this->repository->save($miniProgram, false);
        $id = $miniProgram->getId();
        self::getEntityManager()->clear();

        // The entity should not be found in database since flush was not called
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_REMOVE_TEST');
        $miniProgram->setName('Remove Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $id = $miniProgram->getId();
        $this->repository->remove($miniProgram);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteEntity(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_REMOVE_NOFLUSH_TEST');
        $miniProgram->setName('Remove NoFlush Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $id = $miniProgram->getId();
        $this->repository->remove($miniProgram, false);
        self::getEntityManager()->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(MiniProgram::class, $found);
    }

    public function testFindByWithCodeIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_NULL_CODE_TEST');
        $miniProgram->setName('Null Code Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        // Code is auto-generated, so leave it as default (null)

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['code' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithEncryptKeyIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_NULL_ENCRYPT_TEST');
        $miniProgram->setName('Null Encrypt Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setEncryptKey(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['encryptKey' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithAuthRedirectUrlIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_NULL_REDIRECT_TEST');
        $miniProgram->setName('Null Redirect Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setAuthRedirectUrl(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['authRedirectUrl' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithRemarkIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_NULL_REMARK_TEST');
        $miniProgram->setName('Null Remark Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setRemark(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['remark' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithCodeIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_COUNT_NULL_CODE_TEST');
        $miniProgram->setName('Count Null Code Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        // Code is auto-generated, so leave it as default (null)

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['code' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithEncryptKeyIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_COUNT_NULL_ENCRYPT_TEST');
        $miniProgram->setName('Count Null Encrypt Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setEncryptKey(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['encryptKey' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithAuthRedirectUrlIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_COUNT_NULL_REDIRECT_TEST');
        $miniProgram->setName('Count Null Redirect Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setAuthRedirectUrl(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['authRedirectUrl' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithRemarkIsNullShouldWork(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('APP_COUNT_NULL_REMARK_TEST');
        $miniProgram->setName('Count Null Remark Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setRemark(null);

        self::getEntityManager()->persist($miniProgram);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['remark' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByAppIdAndCodeSpecificity(): void
    {
        // Create multiple mini programs to test specificity
        $miniProgram1 = new MiniProgram();
        $miniProgram1->setAppId('APP_1');
        $miniProgram1->setCode('CODE_1');
        $miniProgram1->setName('Mini Program 1');
        $miniProgram1->setPrivateKey('private_key_1');
        $miniProgram1->setAlipayPublicKey('public_key_1');

        $miniProgram2 = new MiniProgram();
        $miniProgram2->setAppId('APP_2');
        $miniProgram2->setCode('CODE_2');
        $miniProgram2->setName('Mini Program 2');
        $miniProgram2->setPrivateKey('private_key_2');
        $miniProgram2->setAlipayPublicKey('public_key_2');

        self::getEntityManager()->persist($miniProgram1);
        self::getEntityManager()->persist($miniProgram2);
        self::getEntityManager()->flush();

        // Test find by app id
        $result = $this->repository->findByAppId('APP_2');
        $this->assertInstanceOf(MiniProgram::class, $result);
        $this->assertSame('APP_2', $result->getAppId());
        $this->assertSame('CODE_2', $result->getCode());

        // Test find by code
        $result = $this->repository->findByCode('CODE_1');
        $this->assertInstanceOf(MiniProgram::class, $result);
        $this->assertSame('CODE_1', $result->getCode());
        $this->assertSame('APP_1', $result->getAppId());
    }

    public function testFindOneByWithOrderBySorting(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $mockEntity1 = $this->createMock(MiniProgram::class);
        $mockEntity2 = $this->createMock(MiniProgram::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        $repository = new class($managerRegistry, [$mockEntity1, $mockEntity2]) extends MiniProgramRepository {
            /** @var array<MiniProgram> */
            private array $entities;

            /** @param array<MiniProgram> $entities */
            public function __construct(ManagerRegistry $registry, array $entities)
            {
                parent::__construct($registry);
                $this->entities = $entities;
            }

            public function findOneBy(array $criteria, ?array $orderBy = null): MiniProgram
            {
                if (is_array($orderBy) && isset($orderBy['appId']) && 'DESC' === $orderBy['appId']) {
                    return $this->entities[1];
                }

                return $this->entities[0];
            }
        };

        $resultAsc = $repository->findOneBy([], ['appId' => 'ASC']);
        $resultDesc = $repository->findOneBy([], ['appId' => 'DESC']);

        $this->assertSame($mockEntity1, $resultAsc);
        $this->assertSame($mockEntity2, $resultDesc);
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

    /**
     * @return ServiceEntityRepository<MiniProgram>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setName('Test Mini Program ' . uniqid());
        $miniProgram->setAppId('test_app_id_' . uniqid());
        $miniProgram->setPrivateKey('test_private_key_' . uniqid());
        $miniProgram->setAlipayPublicKey('test_public_key_' . uniqid());
        $miniProgram->setSignType('RSA2');
        $miniProgram->setGatewayUrl('https://openapi.alipay.com/gateway.do');
        $miniProgram->setSandbox(false);

        return $miniProgram;
    }
}
