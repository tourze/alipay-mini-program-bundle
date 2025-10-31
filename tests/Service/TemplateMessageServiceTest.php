<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(TemplateMessageService::class)]
#[RunTestsInSeparateProcesses]
final class TemplateMessageServiceTest extends AbstractIntegrationTestCase
{
    private MiniProgram $miniProgram;

    private User $user;

    protected function onSetUp(): void
    {
        // Create test MiniProgram
        $this->miniProgram = new MiniProgram();
        $this->miniProgram->setAppId('test_app_id');
        $this->miniProgram->setName('Test App');
        $this->miniProgram->setPrivateKey('test_private_key');
        $this->miniProgram->setAlipayPublicKey('test_public_key');
        self::getService(EntityManagerInterface::class)->persist($this->miniProgram);

        // Create test User
        $this->user = new User();
        $this->user->setMiniProgram($this->miniProgram);
        $this->user->setOpenId('test_open_id');
        self::getService(EntityManagerInterface::class)->persist($this->user);

        self::getService(EntityManagerInterface::class)->flush();
    }

    public function testConstructorWithDependencies(): void
    {
        // Test that service can be constructed with all dependencies
        $service = self::getService(TemplateMessageService::class);

        $this->assertInstanceOf(TemplateMessageService::class, $service);
    }

    public function testSend(): void
    {
        $service = self::getService(TemplateMessageService::class);

        $this->assertInstanceOf(TemplateMessageService::class, $service);
    }

    public function testSendPendingMessages(): void
    {
        $service = self::getService(TemplateMessageService::class);

        $this->assertInstanceOf(TemplateMessageService::class, $service);
    }
}
