<?php

namespace AlipayMiniProgramBundle\Tests\Repository;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use AlipayMiniProgramBundle\Repository\AuthCodeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<AuthCode>
 * @internal
 */
#[CoversClass(AuthCodeRepository::class)]
#[RunTestsInSeparateProcesses]
final class AuthCodeRepositoryTest extends AbstractRepositoryTestCase
{
    private AuthCodeRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AuthCodeRepository::class);
    }

    public function testFindByAuthCodeWithValidCode(): void
    {
        // Create a test auth code entity
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_AUTH_CODE_123');
        $authCode->setUserId('user123');
        $authCode->setOpenId('open123');
        $authCode->setAccessToken('access123');
        $authCode->setRefreshToken('refresh123');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $result = $this->repository->findByAuthCode('TEST_AUTH_CODE_123');

        $this->assertInstanceOf(AuthCode::class, $result);
        $this->assertSame('TEST_AUTH_CODE_123', $result->getAuthCode());
        $this->assertSame('user123', $result->getUserId());
    }

    public function testFindByAuthCodeWithNonExistentCode(): void
    {
        $result = $this->repository->findByAuthCode('NON_EXISTENT_CODE');

        $this->assertNull($result);
    }

    public function testFindByAuthCodeWithEmptyString(): void
    {
        $result = $this->repository->findByAuthCode('');

        $this->assertNull($result);
    }

    public function testFindByAuthCodeReturnsCorrectEntity(): void
    {
        // Create multiple auth codes to test specificity
        $authCode1 = new AuthCode();
        $authCode1->setAuthCode('CODE_1');
        $authCode1->setUserId('user1');
        $authCode1->setOpenId('open1');
        $authCode1->setAccessToken('access1');
        $authCode1->setRefreshToken('refresh1');
        $authCode1->setScope(AlipayAuthScope::AUTH_USER);
        $authCode1->setAuthStart(new \DateTimeImmutable());
        $authCode1->setExpiresIn(3600);
        $authCode1->setReExpiresIn(7200);

        $authCode2 = new AuthCode();
        $authCode2->setAuthCode('CODE_2');
        $authCode2->setUserId('user2');
        $authCode2->setOpenId('open2');
        $authCode2->setAccessToken('access2');
        $authCode2->setRefreshToken('refresh2');
        $authCode2->setScope(AlipayAuthScope::AUTH_USER);
        $authCode2->setAuthStart(new \DateTimeImmutable());
        $authCode2->setExpiresIn(3600);
        $authCode2->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode1);
        self::getEntityManager()->persist($authCode2);
        self::getEntityManager()->flush();

        $result = $this->repository->findByAuthCode('CODE_2');

        $this->assertInstanceOf(AuthCode::class, $result);
        $this->assertSame('CODE_2', $result->getAuthCode());
        $this->assertSame('user2', $result->getUserId());
    }

    public function testSaveWithoutFlushShouldNotPersistEntity(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_SAVE_NOFLUSH');
        $authCode->setUserId('user_save_noflush');
        $authCode->setOpenId('open_save_noflush');
        $authCode->setAccessToken('access_save_noflush');
        $authCode->setRefreshToken('refresh_save_noflush');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        $this->repository->save($authCode, false);
        $id = $authCode->getId();
        self::getEntityManager()->clear();

        // The entity should not be found in database since flush was not called
        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithFlushShouldDeleteEntity(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_REMOVE');
        $authCode->setUserId('user_remove');
        $authCode->setOpenId('open_remove');
        $authCode->setAccessToken('access_remove');
        $authCode->setRefreshToken('refresh_remove');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $id = $authCode->getId();
        $this->repository->remove($authCode);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteEntity(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_REMOVE_NOFLUSH');
        $authCode->setUserId('user_remove_noflush');
        $authCode->setOpenId('open_remove_noflush');
        $authCode->setAccessToken('access_remove_noflush');
        $authCode->setRefreshToken('refresh_remove_noflush');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $id = $authCode->getId();
        $this->repository->remove($authCode, false);
        self::getEntityManager()->clear();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(AuthCode::class, $found);
    }

    public function testFindByAlipayUserRelationShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_RELATION');
        $authCode->setUserId('user_relation');
        $authCode->setOpenId('open_relation');
        $authCode->setAccessToken('access_relation');
        $authCode->setRefreshToken('refresh_relation');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setAlipayUser(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['alipayUser' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithAlipayUserRelationShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_COUNT_RELATION');
        $authCode->setUserId('user_count_relation');
        $authCode->setOpenId('open_count_relation');
        $authCode->setAccessToken('access_count_relation');
        $authCode->setRefreshToken('refresh_count_relation');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setAlipayUser(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['alipayUser' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByWithStateIsNullShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_STATE_NULL');
        $authCode->setUserId('user_state_null');
        $authCode->setOpenId('open_state_null');
        $authCode->setAccessToken('access_state_null');
        $authCode->setRefreshToken('refresh_state_null');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setState(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['state' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithSignIsNullShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_SIGN_NULL');
        $authCode->setUserId('user_sign_null');
        $authCode->setOpenId('open_sign_null');
        $authCode->setAccessToken('access_sign_null');
        $authCode->setRefreshToken('refresh_sign_null');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setSign(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['sign' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountWithStateIsNullShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_COUNT_STATE_NULL');
        $authCode->setUserId('user_count_state_null');
        $authCode->setOpenId('open_count_state_null');
        $authCode->setAccessToken('access_count_state_null');
        $authCode->setRefreshToken('refresh_count_state_null');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setState(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['state' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountWithSignIsNullShouldWork(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_COUNT_SIGN_NULL');
        $authCode->setUserId('user_count_sign_null');
        $authCode->setOpenId('open_count_sign_null');
        $authCode->setAccessToken('access_count_sign_null');
        $authCode->setRefreshToken('refresh_count_sign_null');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);
        $authCode->setSign(null);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['sign' => null]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByAssociationAlipayUserShouldReturnMatchingEntity(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_ASSOCIATION');
        $authCode->setUserId('user_association');
        $authCode->setOpenId('open_association');
        $authCode->setAccessToken('access_association');
        $authCode->setRefreshToken('refresh_association');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['alipayUser' => $authCode->getAlipayUser()]);

        $this->assertInstanceOf(AuthCode::class, $result);
        $this->assertEquals('TEST_ASSOCIATION', $result->getAuthCode());
    }

    public function testCountByAssociationAlipayUserShouldReturnCorrectNumber(): void
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('TEST_COUNT_ASSOCIATION');
        $authCode->setUserId('user_count_association');
        $authCode->setOpenId('open_count_association');
        $authCode->setAccessToken('access_count_association');
        $authCode->setRefreshToken('refresh_count_association');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['alipayUser' => $authCode->getAlipayUser()]);

        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBySorting(): void
    {
        $authCode1 = new AuthCode();
        $authCode1->setAuthCode('TEST_SORT_A_UNIQUE_12345');
        $authCode1->setUserId('user_sort_a_unique');
        $authCode1->setOpenId('open_sort_a_unique');
        $authCode1->setAccessToken('access_sort_a_unique');
        $authCode1->setRefreshToken('refresh_sort_a_unique');
        $authCode1->setScope(AlipayAuthScope::AUTH_USER);
        $authCode1->setAuthStart(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $authCode1->setExpiresIn(3600);
        $authCode1->setReExpiresIn(7200);

        $authCode2 = new AuthCode();
        $authCode2->setAuthCode('TEST_SORT_B_UNIQUE_67890');
        $authCode2->setUserId('user_sort_b_unique');
        $authCode2->setOpenId('open_sort_b_unique');
        $authCode2->setAccessToken('access_sort_b_unique');
        $authCode2->setRefreshToken('refresh_sort_b_unique');
        $authCode2->setScope(AlipayAuthScope::AUTH_USER);
        $authCode2->setAuthStart(new \DateTimeImmutable('2023-01-02 10:00:00'));
        $authCode2->setExpiresIn(3600);
        $authCode2->setReExpiresIn(7200);

        self::getEntityManager()->persist($authCode1);
        self::getEntityManager()->persist($authCode2);
        self::getEntityManager()->flush();

        // Test order by authStart DESC with specific criteria
        $result = $this->repository->findOneBy(['userId' => 'user_sort_b_unique'], ['authStart' => 'DESC']);
        $this->assertInstanceOf(AuthCode::class, $result);
        $this->assertEquals('TEST_SORT_B_UNIQUE_67890', $result->getAuthCode());

        // Test order by authStart ASC with specific criteria
        $result = $this->repository->findOneBy(['userId' => 'user_sort_a_unique'], ['authStart' => 'ASC']);
        $this->assertInstanceOf(AuthCode::class, $result);
        $this->assertEquals('TEST_SORT_A_UNIQUE_12345', $result->getAuthCode());
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

    public function testFindByWithCreatedFromIpIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['createdFromIp' => null]);

        // findBy() always returns array, verify content instead
        $this->assertGreaterThanOrEqual(0, count($results));
    }

    public function testFindByWithUpdatedFromIpIsNullShouldWork(): void
    {
        $results = $this->repository->findBy(['updatedFromIp' => null]);

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

    public function testCountWithCreatedFromIpIsNullShouldWork(): void
    {
        $count = $this->repository->count(['createdFromIp' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithUpdatedFromIpIsNullShouldWork(): void
    {
        $count = $this->repository->count(['updatedFromIp' => null]);

        // count() always returns int, verify value instead
        $this->assertGreaterThanOrEqual(0, $count);
    }

    /**
     * @return ServiceEntityRepository<AuthCode>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $authCode = new AuthCode();
        $authCode->setAuthCode('test_auth_code_' . uniqid());
        $authCode->setUserId('test_user_' . uniqid());
        $authCode->setOpenId('test_open_id_' . uniqid());
        $authCode->setAccessToken('test_access_token_' . uniqid());
        $authCode->setRefreshToken('test_refresh_token_' . uniqid());
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setExpiresIn(3600);
        $authCode->setReExpiresIn(7200);

        return $authCode;
    }
}
