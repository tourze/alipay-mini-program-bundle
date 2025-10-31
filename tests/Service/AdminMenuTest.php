<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = new class implements LinkGeneratorInterface {
            /** @var array<string, string> */
            private array $responses = [];

            public function setExpectedResponse(string $class, string $response): void
            {
                $this->responses[$class] = $response;
            }

            public function getCurdListPage(string $class): string
            {
                return $this->responses[$class] ?? '/admin/default';
            }

            public function extractEntityFqcn(string $url): string
            {
                return $url;
            }

            public function setDashboard(string $dashboardControllerFqcn): void
            {
                // 满足接口要求，测试中暂不使用
            }
        };
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    protected function getMenuProvider(): object
    {
        return $this->adminMenu;
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->adminMenu);
    }

    public function testAdminMenuServiceExists(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testLinkGeneratorIsConfigured(): void
    {
        $result = $this->linkGenerator->getCurdListPage(MiniProgram::class);
        // Since we haven't set any specific response, it should return the default
        $this->assertEquals('/admin/default', $result);
    }

    public function testLinkGeneratorReturnsDefaultForUnknownClass(): void
    {
        $result = $this->linkGenerator->getCurdListPage('UnknownClass');
        $this->assertEquals('/admin/default', $result);
    }

    public function testEntityInstantiation(): void
    {
        $miniProgram = new MiniProgram();
        $this->assertInstanceOf(MiniProgram::class, $miniProgram);

        $user = new User();
        $this->assertInstanceOf(User::class, $user);

        $authCode = new AuthCode();
        $this->assertInstanceOf(AuthCode::class, $authCode);

        $formId = new FormId();
        $this->assertInstanceOf(FormId::class, $formId);

        $templateMessage = new TemplateMessage();
        $this->assertInstanceOf(TemplateMessage::class, $templateMessage);

        $phone = new Phone();
        $this->assertInstanceOf(Phone::class, $phone);

        $alipayUserPhone = new AlipayUserPhone();
        $this->assertInstanceOf(AlipayUserPhone::class, $alipayUserPhone);
    }
}
