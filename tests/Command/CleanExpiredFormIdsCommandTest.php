<?php

namespace AlipayMiniProgramBundle\Tests\Command;

use AlipayMiniProgramBundle\Command\CleanExpiredFormIdsCommand;
use AlipayMiniProgramBundle\Service\FormIdService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanExpiredFormIdsCommandTest extends TestCase
{
    private FormIdService|MockObject $formIdService;
    private CleanExpiredFormIdsCommand|MockObject $command;

    protected function setUp(): void
    {
        $this->formIdService = $this->createMock(FormIdService::class);
        $this->command = new CleanExpiredFormIdsCommand($this->formIdService);
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
        $this->assertInstanceOf(CleanExpiredFormIdsCommand::class, $this->command);
    }

    public function test_command_name_is_correct(): void
    {
        $this->assertSame('alipay:mini-program:clean-expired-form-ids', $this->command->getName());
    }

    public function test_command_description_is_correct(): void
    {
        $this->assertSame('清理过期的formId', $this->command->getDescription());
    }

    public function test_execute_calls_form_id_service_and_returns_success(): void
    {
        $expectedCount = 5;
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->formIdService
            ->expects($this->once())
            ->method('cleanExpiredFormIds')
            ->willReturn($expectedCount);

        $output
            ->expects($this->once())
            ->method('writeln')
            ->with('清理了 5 个过期的formId');

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_with_zero_cleaned_records(): void
    {
        $expectedCount = 0;
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->formIdService
            ->expects($this->once())
            ->method('cleanExpiredFormIds')
            ->willReturn($expectedCount);

        $output
            ->expects($this->once())
            ->method('writeln')
            ->with('清理了 0 个过期的formId');

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_with_large_number_of_cleaned_records(): void
    {
        $expectedCount = 1000;
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->formIdService
            ->expects($this->once())
            ->method('cleanExpiredFormIds')
            ->willReturn($expectedCount);

        $output
            ->expects($this->once())
            ->method('writeln')
            ->with('清理了 1000 个过期的formId');

        $result = $this->command->run($input, $output);

        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_method_exists(): void
    {
        $this->assertTrue(method_exists($this->command, 'execute'));
    }

    public function test_command_extends_command_class(): void
    {
        $reflection = new \ReflectionClass($this->command);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertSame(Command::class, $parentClass->getName());
    }

    public function test_constructor_requires_form_id_service(): void
    {
        $reflection = new \ReflectionClass(CleanExpiredFormIdsCommand::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('formIdService', $parameter->getName());
        $this->assertSame(FormIdService::class, $parameter->getType()->getName());
    }

    public function test_command_has_as_command_attribute(): void
    {
        $reflection = new \ReflectionClass(CleanExpiredFormIdsCommand::class);
        $attributes = $reflection->getAttributes();

        $hasAsCommandAttribute = false;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Symfony\Component\Console\Attribute\AsCommand') {
                $hasAsCommandAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAsCommandAttribute, 'Command should have AsCommand attribute');
    }

    public function test_execute_returns_integer(): void
    {
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $this->formIdService
            ->expects($this->once())
            ->method('cleanExpiredFormIds')
            ->willReturn(5);

        $output
            ->expects($this->once())
            ->method('writeln');

        $result = $this->command->run($input, $output);

        $this->assertIsInt($result);
    }
}
