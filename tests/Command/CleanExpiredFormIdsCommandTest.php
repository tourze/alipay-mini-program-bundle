<?php

namespace AlipayMiniProgramBundle\Tests\Command;

use AlipayMiniProgramBundle\Command\CleanExpiredFormIdsCommand;
use AlipayMiniProgramBundle\Service\FormIdService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(CleanExpiredFormIdsCommand::class)]
#[RunTestsInSeparateProcesses]
final class CleanExpiredFormIdsCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试使用真实的服务实例
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(CleanExpiredFormIdsCommand::class);

        return new CommandTester($command);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $command = self::getService(CleanExpiredFormIdsCommand::class);
        $this->assertInstanceOf(CleanExpiredFormIdsCommand::class, $command);
    }

    public function testCommandNameIsCorrect(): void
    {
        $command = self::getService(CleanExpiredFormIdsCommand::class);
        $this->assertSame('alipay:mini-program:clean-expired-form-ids', $command->getName());
    }

    public function testCommandDescriptionIsCorrect(): void
    {
        $command = self::getService(CleanExpiredFormIdsCommand::class);
        $this->assertSame('清理过期的formId', $command->getDescription());
    }

    public function testExecuteReturnsSuccess(): void
    {
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('清理了', $commandTester->getDisplay());
        $this->assertStringContainsString('个过期的formId', $commandTester->getDisplay());
    }

    public function testCommandExtendsCommandClass(): void
    {
        $command = self::getService(CleanExpiredFormIdsCommand::class);
        $reflection = new \ReflectionClass($command);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(Command::class, $parentClass->getName());
    }

    public function testConstructorRequiresFormIdService(): void
    {
        $reflection = new \ReflectionClass(CleanExpiredFormIdsCommand::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('formIdService', $parameter->getName());
        $type = $parameter->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame(FormIdService::class, $type->getName());
    }

    public function testCommandHasAsCommandAttribute(): void
    {
        $reflection = new \ReflectionClass(CleanExpiredFormIdsCommand::class);
        $attributes = $reflection->getAttributes();

        $hasAsCommandAttribute = false;
        foreach ($attributes as $attribute) {
            if ('Symfony\Component\Console\Attribute\AsCommand' === $attribute->getName()) {
                $hasAsCommandAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAsCommandAttribute, 'Command should have AsCommand attribute');
    }

    public function testExecuteWithCommandTester(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('清理了', $commandTester->getDisplay());
        $this->assertStringContainsString('个过期的formId', $commandTester->getDisplay());
    }
}
