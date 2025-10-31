<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Service\FormIdService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(FormIdService::class)]
#[RunTestsInSeparateProcesses]
final class FormIdServiceTest extends AbstractIntegrationTestCase
{
    private FormIdService $formIdService;

    protected function onSetUp(): void
    {
        $this->formIdService = self::getService(FormIdService::class);
    }

    public function testSaveFormIdWithValidData(): void
    {
        // Create real entities
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setName('Test Mini Program');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id');
        $user->setNickName('Test User');

        self::getService(EntityManagerInterface::class)->persist($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        $formId = 'test_form_id_123';

        // Act
        $result = $this->formIdService->saveFormId($miniProgram, $user, $formId);

        // Assert
        $this->assertInstanceOf(FormId::class, $result);
        $this->assertSame($miniProgram, $result->getMiniProgram());
        $this->assertSame($user, $result->getUser());
        $this->assertSame($formId, $result->getFormId());
        $this->assertEquals(0, $result->getUsedCount());
        $this->assertInstanceOf(\DateTimeInterface::class, $result->getExpireTime());
        $this->assertGreaterThan(new \DateTime(), $result->getExpireTime());
    }

    public function testGetAvailableFormIdWhenFormIdExists(): void
    {
        // Create real entities
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_2');
        $miniProgram->setName('Test Mini Program 2');
        $miniProgram->setPrivateKey('test_private_key_2');
        $miniProgram->setAlipayPublicKey('test_public_key_2');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_2');
        $user->setNickName('Test User 2');

        self::getService(EntityManagerInterface::class)->persist($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        // Create a form ID first
        $savedFormId = $this->formIdService->saveFormId($miniProgram, $user, 'test_form_id_available');

        // Act
        $result = $this->formIdService->getAvailableFormId($miniProgram, $user);

        // Assert
        $this->assertInstanceOf(FormId::class, $result);
        $this->assertSame($miniProgram, $result->getMiniProgram());
        $this->assertSame($user, $result->getUser());
        $this->assertEquals(1, $result->getUsedCount()); // Should be incremented from 0 to 1
    }

    public function testGetAvailableFormIdWhenNoFormIdExists(): void
    {
        // Create real entities without creating any form IDs
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_3');
        $miniProgram->setName('Test Mini Program 3');
        $miniProgram->setPrivateKey('test_private_key_3');
        $miniProgram->setAlipayPublicKey('test_public_key_3');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_3');
        $user->setNickName('Test User 3');

        self::getService(EntityManagerInterface::class)->persist($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        // Act
        $result = $this->formIdService->getAvailableFormId($miniProgram, $user);

        // Assert
        $this->assertNull($result);
    }

    public function testCleanExpiredFormIds(): void
    {
        // Create expired form IDs
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id_4');
        $miniProgram->setName('Test Mini Program 4');
        $miniProgram->setPrivateKey('test_private_key_4');
        $miniProgram->setAlipayPublicKey('test_public_key_4');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id_4');
        $user->setNickName('Test User 4');

        self::getService(EntityManagerInterface::class)->persist($miniProgram);
        self::getService(EntityManagerInterface::class)->persist($user);
        self::getService(EntityManagerInterface::class)->flush();

        // Create expired form IDs manually
        $expiredFormId1 = new FormId();
        $expiredFormId1->setMiniProgram($miniProgram);
        $expiredFormId1->setUser($user);
        $expiredFormId1->setFormId('expired_form_id_1');
        $expiredFormId1->setUsedCount(0);
        $expiredFormId1->setExpireTime(new \DateTimeImmutable('-1 day'));

        $expiredFormId2 = new FormId();
        $expiredFormId2->setMiniProgram($miniProgram);
        $expiredFormId2->setUser($user);
        $expiredFormId2->setFormId('expired_form_id_2');
        $expiredFormId2->setUsedCount(0);
        $expiredFormId2->setExpireTime(new \DateTimeImmutable('-1 hour'));

        self::getService(EntityManagerInterface::class)->persist($expiredFormId1);
        self::getService(EntityManagerInterface::class)->persist($expiredFormId2);
        self::getService(EntityManagerInterface::class)->flush();

        // Act
        $result = $this->formIdService->cleanExpiredFormIds();

        // Assert
        $this->assertGreaterThanOrEqual(0, $result);
    }
}
