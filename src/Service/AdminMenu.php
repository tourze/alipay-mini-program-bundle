<?php

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Controller\Admin\AlipayUserPhoneCrudController;
use AlipayMiniProgramBundle\Controller\Admin\AuthCodeCrudController;
use AlipayMiniProgramBundle\Controller\Admin\FormIdCrudController;
use AlipayMiniProgramBundle\Controller\Admin\MiniProgramCrudController;
use AlipayMiniProgramBundle\Controller\Admin\PhoneCrudController;
use AlipayMiniProgramBundle\Controller\Admin\TemplateMessageCrudController;
use AlipayMiniProgramBundle\Controller\Admin\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

class AdminMenu
{
    /**
     * 获取支付宝小程序模块的菜单配置
     *
     * @return mixed[]
     */
    public static function getMenuItems(): array
    {
        return [
            MenuItem::section('支付宝小程序', 'fas fa-alipay'),
            
            MenuItem::linkToCrud('小程序配置', 'fas fa-cogs', MiniProgramCrudController::getEntityFqcn())
                ->setController(MiniProgramCrudController::class),
            
            MenuItem::linkToCrud('支付宝用户', 'fas fa-users', UserCrudController::getEntityFqcn())
                ->setController(UserCrudController::class),
            
            MenuItem::linkToCrud('授权码记录', 'fas fa-key', AuthCodeCrudController::getEntityFqcn())
                ->setController(AuthCodeCrudController::class),
            
            MenuItem::linkToCrud('表单ID管理', 'fas fa-clipboard-list', FormIdCrudController::getEntityFqcn())
                ->setController(FormIdCrudController::class),
            
            MenuItem::linkToCrud('模板消息', 'fas fa-envelope', TemplateMessageCrudController::getEntityFqcn())
                ->setController(TemplateMessageCrudController::class),
            
            MenuItem::linkToCrud('手机号码', 'fas fa-mobile-alt', PhoneCrudController::getEntityFqcn())
                ->setController(PhoneCrudController::class),
            
            MenuItem::linkToCrud('用户手机号绑定', 'fas fa-link', AlipayUserPhoneCrudController::getEntityFqcn())
                ->setController(AlipayUserPhoneCrudController::class),
        ];
    }

    /**
     * 获取简化版菜单（只包含主要功能）
     *
     * @return mixed[]
     */
    public static function getSimpleMenuItems(): array
    {
        return [
            MenuItem::section('支付宝小程序', 'fas fa-alipay'),
            
            MenuItem::linkToCrud('小程序配置', 'fas fa-cogs', MiniProgramCrudController::getEntityFqcn())
                ->setController(MiniProgramCrudController::class),
            
            MenuItem::linkToCrud('支付宝用户', 'fas fa-users', UserCrudController::getEntityFqcn())
                ->setController(UserCrudController::class),
            
            MenuItem::linkToCrud('模板消息', 'fas fa-envelope', TemplateMessageCrudController::getEntityFqcn())
                ->setController(TemplateMessageCrudController::class),
        ];
    }
} 