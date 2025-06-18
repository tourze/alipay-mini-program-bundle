<?php

namespace AlipayMiniProgramBundle\Tests\Service;

use AlipayMiniProgramBundle\Controller\Admin\AlipayUserPhoneCrudController;
use AlipayMiniProgramBundle\Controller\Admin\AuthCodeCrudController;
use AlipayMiniProgramBundle\Controller\Admin\FormIdCrudController;
use AlipayMiniProgramBundle\Controller\Admin\MiniProgramCrudController;
use AlipayMiniProgramBundle\Controller\Admin\PhoneCrudController;
use AlipayMiniProgramBundle\Controller\Admin\TemplateMessageCrudController;
use AlipayMiniProgramBundle\Controller\Admin\UserCrudController;
use AlipayMiniProgramBundle\Service\AdminMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SectionMenuItem;
use PHPUnit\Framework\TestCase;

class AdminMenuTest extends TestCase
{
    public function test_get_menu_items_returns_correct_structure(): void
    {
        $menuItems = AdminMenu::getMenuItems();
        $this->assertCount(8, $menuItems); // 1个section + 7个crud链接
    }

    public function test_get_menu_items_contains_section(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        $firstItem = $menuItems[0];
        $this->assertInstanceOf(SectionMenuItem::class, $firstItem);
    }

    public function test_get_menu_items_contains_all_required_controllers(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        // 检查菜单项数量是否正确（1个section + 7个crud链接）
        $this->assertCount(8, $menuItems);

        // 检查第一个是section
        $this->assertInstanceOf(SectionMenuItem::class, $menuItems[0]);

        // 检查其余都是CrudMenuItem
        for ($i = 1; $i < count($menuItems); $i++) {
            $this->assertInstanceOf(CrudMenuItem::class, $menuItems[$i]);
        }
    }

    public function test_get_simple_menu_items_returns_correct_structure(): void
    {
        $menuItems = AdminMenu::getSimpleMenuItems();
        $this->assertCount(4, $menuItems); // 1个section + 3个crud链接
    }

    public function test_get_simple_menu_items_contains_main_controllers_only(): void
    {
        $menuItems = AdminMenu::getSimpleMenuItems();

        // 检查菜单项数量是否正确（1个section + 3个crud链接）
        $this->assertCount(4, $menuItems);

        // 检查第一个是section
        $this->assertInstanceOf(SectionMenuItem::class, $menuItems[0]);

        // 检查其余都是CrudMenuItem
        for ($i = 1; $i < count($menuItems); $i++) {
            $this->assertInstanceOf(CrudMenuItem::class, $menuItems[$i]);
        }
    }

    public function test_get_menu_items_all_items_are_menu_item_instances(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        foreach ($menuItems as $index => $item) {
            $this->assertTrue(
                $item instanceof SectionMenuItem || $item instanceof CrudMenuItem,
                "Item at index {$index} is not a valid MenuItem instance"
            );
        }
    }

    public function test_get_simple_menu_items_all_items_are_menu_item_instances(): void
    {
        $menuItems = AdminMenu::getSimpleMenuItems();

        foreach ($menuItems as $index => $item) {
            $this->assertTrue(
                $item instanceof SectionMenuItem || $item instanceof CrudMenuItem,
                "Item at index {$index} is not a valid MenuItem instance"
            );
        }
    }

    public function test_get_menu_items_section_has_correct_type(): void
    {
        $menuItems = AdminMenu::getMenuItems();
        $sectionItem = $menuItems[0];

        $this->assertInstanceOf(SectionMenuItem::class, $sectionItem);
    }

    public function test_controller_classes_exist_and_are_valid(): void
    {
        $controllers = [
            MiniProgramCrudController::class,
            UserCrudController::class,
            AuthCodeCrudController::class,
            FormIdCrudController::class,
            TemplateMessageCrudController::class,
            PhoneCrudController::class,
            AlipayUserPhoneCrudController::class,
        ];

        foreach ($controllers as $controllerClass) {
            $this->assertTrue(
                class_exists($controllerClass),
                "Controller class {$controllerClass} does not exist"
            );

            $this->assertTrue(
                method_exists($controllerClass, 'getEntityFqcn'),
                "Controller {$controllerClass} does not have getEntityFqcn method"
            );
        }
    }

    public function test_menu_items_are_not_empty(): void
    {
        $menuItems = AdminMenu::getMenuItems();
        $this->assertNotEmpty($menuItems);

        $simpleMenuItems = AdminMenu::getSimpleMenuItems();
        $this->assertNotEmpty($simpleMenuItems);
    }

    public function test_simple_menu_is_subset_of_full_menu(): void
    {
        $fullMenuItems = AdminMenu::getMenuItems();
        $simpleMenuItems = AdminMenu::getSimpleMenuItems();

        // 简单菜单应该比完整菜单少
        $this->assertLessThan(count($fullMenuItems), count($simpleMenuItems));

        // 检查简单菜单的结构
        $this->assertCount(4, $simpleMenuItems); // 1个section + 3个crud
        $this->assertCount(8, $fullMenuItems);   // 1个section + 7个crud
    }

    /**
     * 辅助方法：检查MenuItem是否为section
     */
    private function isMenuItemSection($item): bool
    {
        return $item instanceof SectionMenuItem;
    }
}
