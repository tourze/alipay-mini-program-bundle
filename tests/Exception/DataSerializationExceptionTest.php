<?php

namespace AlipayMiniProgramBundle\Tests\Exception;

use AlipayMiniProgramBundle\Exception\DataSerializationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DataSerializationException::class)]
final class DataSerializationExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return DataSerializationException::class;
    }

    protected function getExpectedParentClass(): string
    {
        return \RuntimeException::class;
    }
}
