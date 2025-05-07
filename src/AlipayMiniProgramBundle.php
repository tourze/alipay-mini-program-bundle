<?php

namespace AlipayMiniProgramBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '支付宝小程序模块')]
class AlipayMiniProgramBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \AntdCpBundle\AntdCpBundle::class => ['all' => true],
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
        ];
    }
}
