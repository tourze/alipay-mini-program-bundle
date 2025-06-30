<?php

namespace AlipayMiniProgramBundle\Tests\Unit;

use AlipayMiniProgramBundle\AlipayMiniProgramBundle;
use PHPUnit\Framework\TestCase;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;

class AlipayMiniProgramBundleTest extends TestCase
{
    public function testBundleImplementsBundleDependencyInterface(): void
    {
        $bundle = new AlipayMiniProgramBundle();
        $this->assertInstanceOf(BundleDependencyInterface::class, $bundle);
    }

    public function testGetBundleDependencies(): void
    {
        $dependencies = AlipayMiniProgramBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(DoctrineIndexedBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[DoctrineIndexedBundle::class]);
    }
}