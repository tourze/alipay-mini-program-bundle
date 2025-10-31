<?php

namespace AlipayMiniProgramBundle\Tests\Exception;

use AlipayMiniProgramBundle\Exception\InvalidMiniProgramConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidMiniProgramConfigurationException::class)]
final class InvalidMiniProgramConfigurationExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return InvalidMiniProgramConfigurationException::class;
    }

    protected function getExpectedParentClass(): string
    {
        return \RuntimeException::class;
    }

    protected function getDefaultMessage(): string
    {
        return 'MiniProgram configuration is incomplete';
    }
}
