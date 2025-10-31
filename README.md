# AlipayMiniProgramBundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![License](https://img.shields.io/packagist/l/tourze/alipay-mini-program-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/alipay-mini-program-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo/master?flag=alipay-mini-program-bundle&style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A comprehensive Symfony bundle for integrating Alipay Mini Program functionality, 
providing user authentication, form ID management, template message sending, and more.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Database Setup](#database-setup)
  - [Mini Program Configuration](#mini-program-configuration)
- [Quick Start](#quick-start)
  - [Basic Configuration](#basic-configuration)
  - [User Service Usage](#user-service-usage)
  - [Form ID Management](#form-id-management)
  - [Template Message Service](#template-message-service)
- [Console Commands](#console-commands)
  - [Clean Expired Form IDs](#clean-expired-form-ids)
  - [Send Pending Template Messages](#send-pending-template-messages)
- [JSON-RPC Procedures](#json-rpc-procedures)
- [Entities](#entities)
- [Admin Interface](#admin-interface)
- [Advanced Usage](#advanced-usage)
  - [Custom Event Listeners](#custom-event-listeners)
  - [Background Processing](#background-processing)
  - [Extending Services](#extending-services)
- [Events](#events)
- [Security](#security)
  - [Key Management](#key-management)
  - [Data Protection](#data-protection)
  - [Security Best Practices](#security-best-practices)
  - [Vulnerability Reporting](#vulnerability-reporting)
- [Requirements](#requirements)
- [Testing](#testing)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [License](#license)

## Features

- **User Management**: Handle Alipay user authentication and information management
- **Form ID Collection**: Collect and manage form IDs for template message sending
- **Template Messages**: Send template messages with automatic queue processing
- **Phone Number Management**: Decrypt and manage user phone numbers
- **Multi-App Support**: Manage multiple Alipay mini programs in one application
- **Admin Interface**: EasyAdmin integration for managing users, messages, and configurations
- **Background Processing**: Symfony Messenger integration for asynchronous operations
- **Automatic Cleanup**: Commands for cleaning expired form IDs and sending pending messages

## Installation

```bash
composer require tourze/alipay-mini-program-bundle
```

## Configuration

### Database Setup

Run the database migrations to create the necessary tables:

```bash
php bin/console doctrine:migrations:migrate
```

### Mini Program Configuration

Configure your Alipay Mini Program settings in the admin interface or programmatically:

```php
use AlipayMiniProgramBundle\Entity\MiniProgram;

$miniProgram = new MiniProgram();
$miniProgram->setAppId('your_app_id');
$miniProgram->setPrivateKey('your_private_key');
$miniProgram->setAlipayPublicKey('alipay_public_key');
$miniProgram->setEncryptKey('your_encrypt_key'); // Optional
```

## Quick Start

### Basic Configuration

Register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    AlipayMiniProgramBundle\AlipayMiniProgramBundle::class => ['all' => true],
];
```

### User Service Usage

```php
use AlipayMiniProgramBundle\Service\UserService;

// Update user information
$userService->updateUserInfo($user, [
    'nick_name' => 'John Doe',
    'avatar' => 'https://example.com/avatar.jpg',
    'province' => 'Guangdong',
    'city' => 'Shenzhen',
    'gender' => 'male'
]);

// Bind phone to user
$userService->bindPhone($user, '13800138000');
```

### Form ID Management

```php
use AlipayMiniProgramBundle\Service\FormIdService;

// Save a form ID
$formId = $formIdService->saveFormId($miniProgram, $user, $formIdString);

// Get an available form ID
$availableFormId = $formIdService->getAvailableFormId($miniProgram, $user);

// Clean expired form IDs
$deletedCount = $formIdService->cleanExpiredFormIds();
```

### Template Message Service

```php
use AlipayMiniProgramBundle\Service\TemplateMessageService;

// Send a template message
$success = $templateMessageService->send($templateMessage);

// Send pending messages (batch processing)
$templateMessageService->sendPendingMessages($limit = 10);
```

## Console Commands

### Clean Expired Form IDs

Command: `alipay:mini-program:clean-expired-form-ids`

Remove expired form IDs from the database:

```bash
php bin/console alipay:mini-program:clean-expired-form-ids
```

### Send Pending Template Messages

Command: `alipay:template-message:send-pending`

Process and send queued template messages:

```bash
php bin/console alipay:template-message:send-pending [--limit=10]
```

Options:
- `--limit`, `-l`: Number of messages to process per run (default: 10)

## JSON-RPC Procedures

The bundle provides the following JSON-RPC procedures:

- `SaveAlipayMiniProgramFormId`: Save form IDs from mini program
- `UploadAlipayMiniProgramAuthCode`: Upload authorization codes
- `UploadAlipayMiniProgramPhoneNumber`: Upload encrypted phone numbers

## Entities

- **User**: Alipay user information including nickname, avatar, location
- **MiniProgram**: Mini program configuration with app ID and keys
- **FormId**: Form IDs for template message sending
- **TemplateMessage**: Template messages with status tracking
- **Phone**: Decrypted phone numbers
- **AuthCode**: Authorization codes from mini program
- **AlipayUserPhone**: User-phone relationships

## Admin Interface

The bundle integrates with EasyAdmin and provides the following admin controllers:

- `UserCrudController`: Manage Alipay users
- `MiniProgramCrudController`: Configure mini programs
- `TemplateMessageCrudController`: View and manage template messages
- `FormIdCrudController`: Monitor form ID collection
- `PhoneCrudController`: Manage phone numbers
- `AuthCodeCrudController`: View authorization codes
- `AlipayUserPhoneCrudController`: Manage user-phone relationships

## Advanced Usage

### Custom Event Listeners

Create custom event listeners to handle template message processing:

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
        // Custom processing logic
    }
}
```

### Background Processing

Configure Symfony Messenger for asynchronous processing:

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            alipay_messages: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'AlipayMiniProgramBundle\Message\UpdateUserInfoMessage': alipay_messages
```

### Extending Services

Extend the bundle services for custom functionality:

```php
use AlipayMiniProgramBundle\Service\UserService;

class CustomUserService extends UserService
{
    public function updateUserInfo(User $user, array $userInfo): void
    {
        // Custom validation
        parent::updateUserInfo($user, $userInfo);
        // Additional processing
    }
}
```

## Events

The bundle dispatches events during template message processing:

- Before sending: Allows modification of message data
- After sending: For logging and post-processing

## Security

### Key Management

- **Private Keys**: Store your private keys securely using environment variables or secure key management systems
- **Public Keys**: Ensure Alipay public keys are properly configured for signature verification
- **Encryption**: Use the encrypt key for sensitive data decryption when provided by Alipay

### Data Protection

- **User Data**: User information is encrypted and stored securely
- **Form IDs**: Form IDs are automatically cleaned up after expiration to prevent accumulation
- **Logging**: All external API calls are logged for audit purposes

### Security Best Practices

```php
// Use environment variables for sensitive configuration
$miniProgram->setPrivateKey($_ENV['ALIPAY_PRIVATE_KEY']);
$miniProgram->setAlipayPublicKey($_ENV['ALIPAY_PUBLIC_KEY']);
$miniProgram->setEncryptKey($_ENV['ALIPAY_ENCRYPT_KEY']);
```

### Vulnerability Reporting

If you discover a security vulnerability, please send an email to security@example.com. 
All security vulnerabilities will be promptly addressed.

## Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM 3.0 or higher
- OpenSSL extension
- EasyAdmin Bundle 4.0 or higher

## Testing

Run the test suite:

```bash
vendor/bin/phpunit
```

Run static analysis:

```bash
vendor/bin/phpstan analyse
```

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes and add tests
4. Run tests: `vendor/bin/phpunit`
5. Run static analysis: `vendor/bin/phpstan analyse`
6. Commit your changes: `git commit -am 'Add new feature'`
7. Push to the branch: `git push origin feature/new-feature`
8. Submit a pull request

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for details on version history.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
