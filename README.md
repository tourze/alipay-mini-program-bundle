# AlipayMiniProgramBundle

支付宝小程序模块，用于管理支付宝小程序相关功能。

## 功能特点

- 支付宝小程序授权管理
- 用户信息获取与解密
- 小程序码生成
- 模板消息发送
- 支付功能集成

## 安装

```bash
composer require symfony-aio/alipay-mini-program-bundle
```

## 使用示例

### 获取用户信息

```php
$alipayMiniProgram = $container->get('alipay_mini_program');
$userInfo = $alipayMiniProgram->getUserInfo($code);
```

### 生成小程序码

```php
$response = $alipayMiniProgram->createQrCode([
    'page' => 'pages/index/index',
    'scene' => 'a=1'
]);
```

### 发送模板消息

```php
$result = $alipayMiniProgram->sendTemplateMessage([
    'touser' => '用户的openid',
    'template_id' => '模板ID',
    'data' => [
        'keyword1' => ['value' => '示例内容1'],
        'keyword2' => ['value' => '示例内容2']
    ]
]);
```

## API文档

详细的API文档请参考[支付宝小程序开放文档](https://opendocs.alipay.com/mini)。

## 许可证

本项目采用 MIT 许可证，详情请参阅 LICENSE 文件。
