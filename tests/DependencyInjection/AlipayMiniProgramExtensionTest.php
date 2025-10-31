<?php

namespace AlipayMiniProgramBundle\Tests\DependencyInjection;

use AlipayMiniProgramBundle\DependencyInjection\AlipayMiniProgramExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * AlipayMiniProgramExtension 的单元测试
 *
 * @internal
 */
#[CoversClass(AlipayMiniProgramExtension::class)]
final class AlipayMiniProgramExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    /**
     * 测试别名
     */
    public function testGetAlias(): void
    {
        $extension = new AlipayMiniProgramExtension();
        $alias = $extension->getAlias();
        $this->assertEquals('alipay_mini_program', $alias);
    }
}
