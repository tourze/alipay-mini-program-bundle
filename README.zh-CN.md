# AlipayMiniProgramBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![License](https://img.shields.io/packagist/l/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/master?flag=alipay-mini-program-bundle&style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

一个全面的 Symfony Bundle，用于集成支付宝小程序功能，提供用户认证、Form ID 管理、模板消息发送等功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
  - [数据库设置](#数据库设置)
  - [小程序配置](#小程序配置)
- [快速开始](#快速开始)
  - [基本配置](#基本配置)
  - [用户服务使用](#用户服务使用)
  - [Form ID 管理](#form-id-管理)
  - [模板消息服务](#模板消息服务)
- [控制台命令](#控制台命令)
  - [清理过期的 Form ID](#清理过期的-form-id)
  - [发送待处理的模板消息](#发送待处理的模板消息)
- [JSON-RPC 过程](#json-rpc-过程)
- [实体](#实体)
- [管理界面](#管理界面)
- [高级用法](#高级用法)
  - [自定义事件监听器](#自定义事件监听器)
  - [后台处理](#后台处理)
  - [扩展服务](#扩展服务)
- [事件](#事件)
- [安全性](#安全性)
  - [密钥管理](#密钥管理)
  - [数据保护](#数据保护)
  - [安全最佳实践](#安全最佳实践)
  - [漏洞报告](#漏洞报告)
- [系统要求](#系统要求)
- [测试](#测试)
- [贡献](#贡献)
- [更新日志](#更新日志)
- [许可证](#许可证)

## 功能特性

- **用户管理**：处理支付宝用户认证和信息管理
- **Form ID 收集**：收集和管理用于模板消息发送的 Form ID
- **模板消息**：发送模板消息，支持自动队列处理
- **手机号管理**：解密和管理用户手机号
- **多应用支持**：在一个应用中管理多个支付宝小程序
- **管理界面**：EasyAdmin 集成，用于管理用户、消息和配置
- **后台处理**：Symfony Messenger 集成，支持异步操作
- **自动清理**：提供清理过期 Form ID 和发送待处理消息的命令

## 安装

```bash
composer require tourze/alipay-mini-program-bundle
```

## 配置

### 数据库设置

运行数据库迁移来创建必要的表：

```bash
php bin/console doctrine:migrations:migrate
```

### 小程序配置

在管理界面或通过代码配置你的支付宝小程序设置：

```php
use AlipayMiniProgramBundle\Entity\MiniProgram;

$miniProgram = new MiniProgram();
$miniProgram->setAppId('your_app_id');
$miniProgram->setPrivateKey('your_private_key');
$miniProgram->setAlipayPublicKey('alipay_public_key');
$miniProgram->setEncryptKey('your_encrypt_key'); // 可选
```

## 快速开始

### 基本配置

在 `config/bundles.php` 中注册 Bundle：

```php
return [
    // ...
    AlipayMiniProgramBundle\AlipayMiniProgramBundle::class => ['all' => true],
];
```

### 用户服务使用

```php
use AlipayMiniProgramBundle\Service\UserService;

// 更新用户信息
$userService->updateUserInfo($user, [
    'nick_name' => '张三',
    'avatar' => 'https://example.com/avatar.jpg',
    'province' => '广东',
    'city' => '深圳',
    'gender' => 'male'
]);

// 绑定手机号到用户
$userService->bindPhone($user, '13800138000');
```

### Form ID 管理

```php
use AlipayMiniProgramBundle\Service\FormIdService;

// 保存 Form ID
$formId = $formIdService->saveFormId($miniProgram, $user, $formIdString);

// 获取可用的 Form ID
$availableFormId = $formIdService->getAvailableFormId($miniProgram, $user);

// 清理过期的 Form ID
$deletedCount = $formIdService->cleanExpiredFormIds();
```

### 模板消息服务

```php
use AlipayMiniProgramBundle\Service\TemplateMessageService;

// 发送模板消息
$success = $templateMessageService->send($templateMessage);

// 发送待处理消息（批量处理）
$templateMessageService->sendPendingMessages($limit = 10);
```

## 控制台命令

### 清理过期的 Form ID

命令：`alipay:mini-program:clean-expired-form-ids`

从数据库中删除过期的 Form ID：

```bash
php bin/console alipay:mini-program:clean-expired-form-ids
```

### 发送待处理的模板消息

命令：`alipay:template-message:send-pending`

处理并发送队列中的模板消息：

```bash
php bin/console alipay:template-message:send-pending [--limit=10]
```

选项：
- `--limit`, `-l`：每次运行处理的消息数量（默认：10）

## JSON-RPC 过程

本 Bundle 提供以下 JSON-RPC 过程：

- `SaveAlipayMiniProgramFormId`：保存来自小程序的 Form ID
- `UploadAlipayMiniProgramAuthCode`：上传授权码
- `UploadAlipayMiniProgramPhoneNumber`：上传加密的手机号

## 实体

- **User**：支付宝用户信息，包括昵称、头像、位置
- **MiniProgram**：小程序配置，包含 App ID 和密钥
- **FormId**：用于模板消息发送的 Form ID
- **TemplateMessage**：带状态跟踪的模板消息
- **Phone**：解密后的手机号
- **AuthCode**：来自小程序的授权码
- **AlipayUserPhone**：用户-手机号关系

## 管理界面

本 Bundle 与 EasyAdmin 集成，提供以下管理控制器：

- `UserCrudController`：管理支付宝用户
- `MiniProgramCrudController`：配置小程序
- `TemplateMessageCrudController`：查看和管理模板消息
- `FormIdCrudController`：监控 Form ID 收集
- `PhoneCrudController`：管理手机号
- `AuthCodeCrudController`：查看授权码
- `AlipayUserPhoneCrudController`：管理用户-手机号关系

## 高级用法

### 自定义事件监听器

创建自定义事件监听器来处理模板消息：

```php
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: TemplateMessage::class)]
class CustomTemplateMessageListener
{
    public function postPersist(TemplateMessage $message, PostPersistEventArgs $args): void
    {
        // 自定义处理逻辑
    }
}
```

### 后台处理

配置 Symfony Messenger 进行异步处理：

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            alipay_messages: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'AlipayMiniProgramBundle\Message\UpdateUserInfoMessage': alipay_messages
```

### 扩展服务

为自定义功能扩展 Bundle 服务：

```php
use AlipayMiniProgramBundle\Service\UserService;

class CustomUserService extends UserService
{
    public function updateUserInfo(User $user, array $userInfo): void
    {
        // 自定义验证
        parent::updateUserInfo($user, $userInfo);
        // 额外处理
    }
}
```

## 事件

本 Bundle 在模板消息处理过程中分发事件：

- 发送前：允许修改消息数据
- 发送后：用于日志记录和后处理

## 安全性

### 密钥管理

- **私钥**：使用环境变量或安全的密钥管理系统安全存储您的私钥
- **公钥**：确保正确配置支付宝公钥用于签名验证
- **加密**：当支付宝提供时，使用加密密钥进行敏感数据解密

### 数据保护

- **用户数据**：用户信息经过加密并安全存储
- **Form ID**：Form ID 在过期后会自动清理，防止积累
- **日志记录**：所有外部 API 调用都会记录日志以供审计

### 安全最佳实践

```php
// 使用环境变量进行敏感配置
$miniProgram->setPrivateKey($_ENV['ALIPAY_PRIVATE_KEY']);
$miniProgram->setAlipayPublicKey($_ENV['ALIPAY_PUBLIC_KEY']);
$miniProgram->setEncryptKey($_ENV['ALIPAY_ENCRYPT_KEY']);
```

### 漏洞报告

如果您发现安全漏洞，请发送邮件到 security@example.com。
所有安全漏洞都将得到及时处理。

## 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0 或更高版本
- OpenSSL 扩展
- EasyAdmin Bundle 4.0 或更高版本

## 测试

运行测试套件：

```bash
vendor/bin/phpunit
```

运行静态分析：

```bash
vendor/bin/phpstan analyse
```

## 贡献

欢迎贡献！请遵循以下指南：

1. Fork 仓库
2. 创建功能分支：`git checkout -b feature/new-feature`
3. 进行更改并添加测试
4. 运行测试：`vendor/bin/phpunit`
5. 运行静态分析：`vendor/bin/phpstan analyse`
6. 提交更改：`git commit -am 'Add new feature'`
7. 推送到分支：`git push origin feature/new-feature`
8. 提交 Pull Request

## 更新日志

请查看 [CHANGELOG.md](CHANGELOG.md) 了解版本历史详情。

## 许可证

MIT 许可证 (MIT)。更多信息请查看 [LICENSE](LICENSE) 文件。
