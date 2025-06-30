<?php

namespace AlipayMiniProgramBundle\Tests\DependencyInjection;

use AlipayMiniProgramBundle\DependencyInjection\AlipayMiniProgramExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * AlipayMiniProgramExtension 的单元测试
 */
class AlipayMiniProgramExtensionTest extends TestCase
{
    private AlipayMiniProgramExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new AlipayMiniProgramExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * 测试扩展继承正确的基类
     */
    public function testExtendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    /**
     * 测试加载配置
     */
    public function testLoad(): void
    {
        $configs = [];
        
        // 执行加载
        $this->extension->load($configs, $this->container);
        
        // 验证容器不为空（至少应该加载了一些服务）
        $this->assertNotEmpty($this->container->getDefinitions());
    }

    /**
     * 测试加载后服务存在
     */
    public function testServicesLoaded(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);
        
        // 验证一些核心服务已加载
        $this->assertTrue($this->container->hasDefinition('AlipayMiniProgramBundle\Service\UserService'));
        $this->assertTrue($this->container->hasDefinition('AlipayMiniProgramBundle\Service\FormIdService'));
        $this->assertTrue($this->container->hasDefinition('AlipayMiniProgramBundle\Service\TemplateMessageService'));
    }

    /**
     * 测试别名
     */
    public function testGetAlias(): void
    {
        $alias = $this->extension->getAlias();
        $this->assertEquals('alipay_mini_program', $alias);
    }
}