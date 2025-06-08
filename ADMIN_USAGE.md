# 支付宝小程序后台管理使用说明

## 概述

本文档介绍如何在 EasyAdmin 后台中集成和使用支付宝小程序模块的管理功能。

## 控制器列表

支付宝小程序模块提供了以下后台管理控制器：

### 1. 小程序配置管理 (MiniProgramCrudController)
- **路径**: `/alipay-mini-program/mini-program`
- **功能**: 管理支付宝小程序的基本配置信息
- **主要字段**: 小程序名称、AppID、应用私钥、支付宝公钥、AES密钥、沙箱环境配置等

### 2. 支付宝用户管理 (UserCrudController)
- **路径**: `/alipay-mini-program/user`
- **功能**: 管理通过小程序授权的支付宝用户信息
- **主要字段**: 用户OpenID、昵称、头像、省份、城市、性别等
- **注意**: 用户只能通过授权创建，不允许手动新建

### 3. 授权码记录 (AuthCodeCrudController)
- **路径**: `/alipay-mini-program/auth-code`
- **功能**: 查看支付宝小程序用户授权码记录
- **主要字段**: 授权码、授权范围、访问令牌、刷新令牌等
- **注意**: 授权码记录为只读，不允许修改或删除

### 4. 表单ID管理 (FormIdCrudController)
- **路径**: `/alipay-mini-program/form-id`
- **功能**: 管理小程序表单ID，用于发送模板消息
- **主要字段**: 表单ID、过期时间、使用次数等
- **注意**: 表单ID只能通过小程序提交生成，不允许手动创建

### 5. 模板消息管理 (TemplateMessageCrudController)
- **路径**: `/alipay-mini-program/template-message`
- **功能**: 管理支付宝小程序模板消息发送记录
- **主要字段**: 模板ID、接收者、模板数据、发送状态、发送结果等

### 6. 手机号码管理 (PhoneCrudController)
- **路径**: `/alipay-mini-program/phone`
- **功能**: 管理支付宝用户绑定的手机号码
- **主要字段**: 手机号码、绑定用户数等
- **注意**: 手机号码只能通过用户绑定创建，不允许手动创建

### 7. 用户手机号绑定 (AlipayUserPhoneCrudController)
- **路径**: `/alipay-mini-program/user-phone`
- **功能**: 管理支付宝用户与手机号码的绑定关系
- **主要字段**: 支付宝用户、手机号码、验证时间等
- **注意**: 绑定关系不允许手动创建或编辑

## 集成方法

### 1. 在 DashboardController 中集成菜单

```php
<?php

namespace App\Controller\Admin;

use AlipayMiniProgramBundle\Service\AdminMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('首页', 'fa fa-home');
        
        // 其他菜单项...
        
        // 集成支付宝小程序菜单
        yield from AdminMenu::getMenuItems();
        
        // 或者使用简化版菜单
        // yield from AdminMenu::getSimpleMenuItems();
        
        // 其他菜单项...
    }
}
```

### 2. 使用简化版菜单

如果只需要核心功能，可以使用简化版菜单：

```php
yield from AdminMenu::getSimpleMenuItems();
```

简化版菜单只包含：
- 小程序配置
- 支付宝用户
- 模板消息

## 特殊功能说明

### 1. 手机号码格式化显示
手机号码在列表页面会自动格式化显示，例如：`138****1234`

### 2. 枚举字段中文显示
- 授权范围：`auth_base` 显示为"基础授权"，`auth_user` 显示为"用户信息授权"
- 用户性别：`M` 显示为"男"，`F` 显示为"女"

### 3. 时间格式化
所有时间字段统一使用 `Y-m-d H:i:s` 格式显示

### 4. 敏感信息保护
- 授权码、访问令牌、刷新令牌等敏感信息在列表页面只显示前20个字符
- 应用私钥、支付宝公钥等敏感配置只在表单页面显示

### 5. 状态展示
- 表单ID的过期状态和使用状态会用不同颜色显示
- 模板消息的发送结果会用绿色（成功）或红色（失败）显示

## 权限控制

所有控制器都使用 `AdminCrud` 注解进行路由配置，支持：
- 自定义路由路径
- 自定义路由名称
- 与现有权限系统集成

## 性能优化

控制器中已实现：
- 关联查询优化，减少N+1查询问题
- 合适的字段显示策略，减少不必要的数据加载
- 搜索字段配置，提高查询效率

## 注意事项

1. **数据安全**: 敏感配置信息（如私钥）需要妥善保管
2. **操作限制**: 部分实体（如用户、授权码）不允许手动创建，只能通过业务流程生成
3. **关联关系**: 删除操作时注意检查关联关系，避免数据不一致
4. **环境配置**: 沙箱环境和生产环境的配置需要区分管理 