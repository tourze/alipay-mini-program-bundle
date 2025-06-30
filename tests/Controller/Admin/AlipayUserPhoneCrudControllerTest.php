<?php

namespace AlipayMiniProgramBundle\Tests\Controller\Admin;

use AlipayMiniProgramBundle\Controller\Admin\AlipayUserPhoneCrudController;
use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * AlipayUserPhoneCrudController 的单元测试
 */
class AlipayUserPhoneCrudControllerTest extends TestCase
{
    private AlipayUserPhoneCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new AlipayUserPhoneCrudController();
    }

    /**
     * 测试控制器继承正确的基类
     */
    public function testExtendsAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    /**
     * 测试获取实体FQCN
     */
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals(AlipayUserPhone::class, AlipayUserPhoneCrudController::getEntityFqcn());
    }

    /**
     * 测试配置CRUD方法
     */
    public function testConfigureCrud(): void
    {
        $crud = Crud::new();
        $result = $this->controller->configureCrud($crud);

        $this->assertInstanceOf(Crud::class, $result);
    }

    /**
     * 测试配置字段方法
     */
    public function testConfigureFields(): void
    {
        // 测试方法存在并能正常调用
        $fields = $this->controller->configureFields(Crud::PAGE_INDEX);
        $this->assertInstanceOf(\Traversable::class, $fields);
        
        // 转换为数组以便进行基本验证
        $fieldsArray = iterator_to_array($fields);
        $this->assertNotEmpty($fieldsArray);
    }

    /**
     * 测试是否有AdminCrud属性
     */
    public function testHasAdminCrudAttribute(): void
    {
        $reflection = new ReflectionClass($this->controller);
        $attributes = $reflection->getAttributes();

        $hasAdminCrudAttribute = false;
        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'AdminCrud')) {
                $hasAdminCrudAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAdminCrudAttribute, 'Controller should have AdminCrud attribute');
    }

    /**
     * 测试配置动作方法
     */
    public function testConfigureActions(): void
    {
        // 测试方法存在并返回正确类型
        $reflection = new \ReflectionMethod($this->controller, 'configureActions');
        $this->assertTrue($reflection->isPublic());
        $returnType = $reflection->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertEquals(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions::class, $returnType->getName());
    }

    /**
     * 测试配置过滤器方法
     */
    public function testConfigureFilters(): void
    {
        $filters = $this->controller->configureFilters(
            \EasyCorp\Bundle\EasyAdminBundle\Config\Filters::new()
        );

        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Config\Filters::class, $filters);
    }
}