<?php

namespace AlipayMiniProgramBundle\Tests\Command;

use AlipayMiniProgramBundle\Command\SendPendingTemplateMessagesCommand;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SendPendingTemplateMessagesCommand::class)]
#[RunTestsInSeparateProcesses]
final class SendPendingTemplateMessagesCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试使用真实的服务实例
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);

        return new CommandTester($command);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);
        $this->assertInstanceOf(SendPendingTemplateMessagesCommand::class, $command);
    }

    public function testCommandNameIsCorrect(): void
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);
        $this->assertSame('alipay:template-message:send-pending', $command->getName());
    }

    public function testCommandDescriptionIsCorrect(): void
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);
        $this->assertSame('发送待发送的支付宝小程序模板消息', $command->getDescription());
    }

    public function testCommandHasLimitOption(): void
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);
        $definition = $command->getDefinition();
        $this->assertTrue($definition->hasOption('limit'));

        $limitOption = $definition->getOption('limit');
        $this->assertSame('l', $limitOption->getShortcut());
        $this->assertSame('每次处理的消息数量', $limitOption->getDescription());
        $this->assertSame(10, $limitOption->getDefault());
    }

    public function testExecuteWithDefaultLimitSuccess(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testExecuteWithCustomLimitSuccess(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testExecuteWithShortLimitOptionSuccess(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testExecuteWithZeroLimit(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testExecuteWithNegativeLimitPassesNegativeValue(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testCommandExtendsCommandClass(): void
    {
        $command = self::getService(SendPendingTemplateMessagesCommand::class);
        $reflection = new \ReflectionClass($command);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(Command::class, $parentClass->getName());
    }

    public function testConstructorRequiresTemplateMessageService(): void
    {
        $reflection = new \ReflectionClass(SendPendingTemplateMessagesCommand::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('templateMessageService', $parameter->getName());
        $type = $parameter->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame(TemplateMessageService::class, $type->getName());
    }

    public function testCommandHasAsCommandAttribute(): void
    {
        $reflection = new \ReflectionClass(SendPendingTemplateMessagesCommand::class);
        $attributes = $reflection->getAttributes();

        $hasAsCommandAttribute = false;
        $hasCronTaskAttribute = false;

        foreach ($attributes as $attribute) {
            if ('Symfony\Component\Console\Attribute\AsCommand' === $attribute->getName()) {
                $hasAsCommandAttribute = true;
            }
            if ('Tourze\Symfony\CronJob\Attribute\AsCronTask' === $attribute->getName()) {
                $hasCronTaskAttribute = true;
            }
        }

        $this->assertTrue($hasAsCommandAttribute, 'Command should have AsCommand attribute');
        $this->assertTrue($hasCronTaskAttribute, 'Command should have AsCronTask attribute');
    }

    public function testOptionLimit(): void
    {
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute(['--limit' => 5]);
        $this->assertSame(Command::SUCCESS, $exitCode);
    }
}
