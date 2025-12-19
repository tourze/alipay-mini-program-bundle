<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests;

use AlipayMiniProgramBundle\AlipayMiniProgramBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayMiniProgramBundle::class)]
#[RunTestsInSeparateProcesses]
final class AlipayMiniProgramBundleTest extends AbstractBundleTestCase
{
}
