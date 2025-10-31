<?php

namespace AlipayMiniProgramBundle\Tests\Exception;

use AlipayMiniProgramBundle\Exception\InvalidResponseException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(InvalidResponseException::class)]
final class InvalidResponseExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return InvalidResponseException::class;
    }

    protected function getExpectedParentClass(): string
    {
        return \RuntimeException::class;
    }
}
