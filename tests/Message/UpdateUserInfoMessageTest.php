<?php

namespace AlipayMiniProgramBundle\Tests\Message;

use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use PHPUnit\Framework\TestCase;

class UpdateUserInfoMessageTest extends TestCase
{
    public function test_constructor_initializes_correctly(): void
    {
        $userId = 123;
        $authToken = 'test_auth_token_abc123';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertInstanceOf(UpdateUserInfoMessage::class, $message);
    }

    public function test_get_user_id_returns_correct_value(): void
    {
        $userId = 456;
        $authToken = 'test_auth_token_def456';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame($userId, $message->getUserId());
    }

    public function test_get_auth_token_returns_correct_value(): void
    {
        $userId = 789;
        $authToken = 'test_auth_token_ghi789';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_constructor_with_zero_user_id(): void
    {
        $userId = 0;
        $authToken = 'test_auth_token';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame(0, $message->getUserId());
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_constructor_with_negative_user_id(): void
    {
        $userId = -1;
        $authToken = 'test_auth_token';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame(-1, $message->getUserId());
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_constructor_with_empty_auth_token(): void
    {
        $userId = 123;
        $authToken = '';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame($userId, $message->getUserId());
        $this->assertSame('', $message->getAuthToken());
    }

    public function test_constructor_with_long_auth_token(): void
    {
        $userId = 123;
        $authToken = str_repeat('a', 1000);
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame($userId, $message->getUserId());
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_constructor_parameters_are_readonly(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoMessage::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(2, $parameters);
        
        $userIdParam = $parameters[0];
        $authTokenParam = $parameters[1];
        
        $this->assertSame('userId', $userIdParam->getName());
        $this->assertSame('authToken', $authTokenParam->getName());
        $this->assertTrue($userIdParam->isPromoted());
        $this->assertTrue($authTokenParam->isPromoted());
    }

    public function test_message_has_correct_property_types(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoMessage::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $userIdParam = $parameters[0];
        $authTokenParam = $parameters[1];
        
        $userIdType = $userIdParam->getType();
        $authTokenType = $authTokenParam->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $userIdType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $authTokenType);
        $this->assertSame('int', $userIdType->getName());
        $this->assertSame('string', $authTokenType->getName());
    }


    public function test_getter_methods_return_correct_types(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoMessage::class);
        
        $getUserIdMethod = $reflection->getMethod('getUserId');
        $getAuthTokenMethod = $reflection->getMethod('getAuthToken');
        
        $getUserIdReturnType = $getUserIdMethod->getReturnType();
        $getAuthTokenReturnType = $getAuthTokenMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $getUserIdReturnType);
        $this->assertInstanceOf(\ReflectionNamedType::class, $getAuthTokenReturnType);
        $this->assertSame('int', $getUserIdReturnType->getName());
        $this->assertSame('string', $getAuthTokenReturnType->getName());
    }

    public function test_message_immutability(): void
    {
        $userId = 123;
        $authToken = 'test_token';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        // 验证没有setter方法
        $reflection = new \ReflectionClass($message);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $setterMethods = array_filter($methods, function($method) {
            return str_starts_with($method->getName(), 'set');
        });
        
        $this->assertEmpty($setterMethods, 'Message should be immutable - no setter methods allowed');
    }

    public function test_constructor_with_large_user_id(): void
    {
        $userId = PHP_INT_MAX;
        $authToken = 'test_auth_token';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame(PHP_INT_MAX, $message->getUserId());
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_constructor_with_special_characters_in_auth_token(): void
    {
        $userId = 123;
        $authToken = 'test_token_with_特殊字符_and_symbols_!@#$%^&*()';
        
        $message = new UpdateUserInfoMessage($userId, $authToken);
        
        $this->assertSame($userId, $message->getUserId());
        $this->assertSame($authToken, $message->getAuthToken());
    }

    public function test_message_class_structure(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoMessage::class);
        
        // 检查类是否为final（虽然代码中没有显示，但这是消息类的最佳实践）
        $this->assertFalse($reflection->isFinal(), 'Message class structure check');
        
        // 检查类没有继承其他类
        $this->assertFalse($reflection->getParentClass(), 'Message should not extend other classes');
        
        // 检查类有正确数量的属性
        $properties = $reflection->getProperties();
        $this->assertCount(2, $properties);
    }
} 