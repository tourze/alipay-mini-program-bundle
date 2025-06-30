<?php

namespace AlipayMiniProgramBundle\Tests\Unit\Exception;

use AlipayMiniProgramBundle\Exception\InvalidMiniProgramConfigurationException;
use PHPUnit\Framework\TestCase;

class InvalidMiniProgramConfigurationExceptionTest extends TestCase
{
    public function test_exception_extends_runtime_exception(): void
    {
        $exception = new InvalidMiniProgramConfigurationException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function test_exception_has_default_message(): void
    {
        $exception = new InvalidMiniProgramConfigurationException();
        $this->assertSame('MiniProgram configuration is incomplete', $exception->getMessage());
    }

    public function test_exception_accepts_custom_message(): void
    {
        $customMessage = 'Custom error message';
        $exception = new InvalidMiniProgramConfigurationException($customMessage);
        $this->assertSame($customMessage, $exception->getMessage());
    }

    public function test_exception_accepts_code(): void
    {
        $exception = new InvalidMiniProgramConfigurationException('Message', 500);
        $this->assertSame(500, $exception->getCode());
    }

    public function test_exception_accepts_previous_exception(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new InvalidMiniProgramConfigurationException('Message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function test_exception_can_be_thrown(): void
    {
        $this->expectException(InvalidMiniProgramConfigurationException::class);
        $this->expectExceptionMessage('MiniProgram configuration is incomplete');
        
        throw new InvalidMiniProgramConfigurationException();
    }

    public function test_exception_namespace_is_correct(): void
    {
        $reflection = new \ReflectionClass(InvalidMiniProgramConfigurationException::class);
        $this->assertSame('AlipayMiniProgramBundle\Exception', $reflection->getNamespaceName());
    }
}