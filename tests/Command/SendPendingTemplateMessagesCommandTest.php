<?php

namespace AlipayMiniProgramBundle\Tests\Command;

use AlipayMiniProgramBundle\Command\SendPendingTemplateMessagesCommand;
use AlipayMiniProgramBundle\Service\TemplateMessageService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class SendPendingTemplateMessagesCommandTest extends TestCase
{
    private TemplateMessageService|MockObject $templateMessageService;
    private SendPendingTemplateMessagesCommand $command;

    protected function setUp(): void
    {
        $this->templateMessageService = $this->createMock(TemplateMessageService::class);
        $this->command = new SendPendingTemplateMessagesCommand($this->templateMessageService);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
        $this->assertInstanceOf(SendPendingTemplateMessagesCommand::class, $this->command);
    }

    public function test_command_name_is_correct(): void
    {
        $this->assertSame('alipay:template-message:send-pending', $this->command->getName());
    }

    public function test_command_description_is_correct(): void
    {
        $this->assertSame('发送待发送的支付宝小程序模板消息', $this->command->getDescription());
    }

    public function test_command_has_limit_option(): void
    {
        $definition = $this->command->getDefinition();
        $this->assertTrue($definition->hasOption('limit'));

        $limitOption = $definition->getOption('limit');
        $this->assertSame('l', $limitOption->getShortcut());
        $this->assertSame('每次处理的消息数量', $limitOption->getDescription());
        $this->assertSame(10, $limitOption->getDefault());
    }

    public function test_execute_with_default_limit_success(): void
    {
        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->with(10);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('开始处理待发送的模板消息，每次处理 10 条', $commandTester->getDisplay());
        $this->assertStringContainsString('模板消息发送完成', $commandTester->getDisplay());
    }

    public function test_execute_with_custom_limit_success(): void
    {
        $customLimit = 20;

        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->with($customLimit);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute(['--limit' => $customLimit]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('开始处理待发送的模板消息，每次处理 20 条', $commandTester->getDisplay());
        $this->assertStringContainsString('模板消息发送完成', $commandTester->getDisplay());
    }

    public function test_execute_with_short_limit_option_success(): void
    {
        $customLimit = 5;

        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->with($customLimit);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute(['-l' => $customLimit]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('开始处理待发送的模板消息，每次处理 5 条', $commandTester->getDisplay());
    }

    public function test_execute_with_exception_returns_failure(): void
    {
        $exception = new \RuntimeException('Test exception message');

        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->willThrowException($exception);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('发送模板消息时发生错误：Test exception message', $commandTester->getDisplay());
    }

    public function test_execute_with_zero_limit(): void
    {
        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->with(0);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute(['--limit' => 0]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('开始处理待发送的模板消息，每次处理 0 条', $commandTester->getDisplay());
    }

    public function test_execute_with_negative_limit_passes_negative_value(): void
    {
        $this->templateMessageService
            ->expects($this->once())
            ->method('sendPendingMessages')
            ->with(-5); // 负数转换为int仍然是负数

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute(['--limit' => -5]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function test_command_extends_command_class(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(Command::class, $parentClass->getName());
    }

    public function test_constructor_requires_template_message_service(): void
    {
        $reflection = new \ReflectionClass(SendPendingTemplateMessagesCommand::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('templateMessageService', $parameter->getName());
        $type = $parameter->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame(TemplateMessageService::class, $type->getName());
    }

    public function test_command_has_as_command_attribute(): void
    {
        $reflection = new \ReflectionClass(SendPendingTemplateMessagesCommand::class);
        $attributes = $reflection->getAttributes();

        $hasAsCommandAttribute = false;
        $hasCronTaskAttribute = false;

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Symfony\Component\Console\Attribute\AsCommand') {
                $hasAsCommandAttribute = true;
            }
            if ($attribute->getName() === 'Tourze\Symfony\CronJob\Attribute\AsCronTask') {
                $hasCronTaskAttribute = true;
            }
        }

        $this->assertTrue($hasAsCommandAttribute, 'Command should have AsCommand attribute');
        $this->assertTrue($hasCronTaskAttribute, 'Command should have AsCronTask attribute');
    }


}
