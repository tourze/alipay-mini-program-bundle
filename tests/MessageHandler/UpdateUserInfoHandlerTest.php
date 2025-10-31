<?php

namespace AlipayMiniProgramBundle\Tests\MessageHandler;

use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\MessageHandler\UpdateUserInfoHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(UpdateUserInfoHandler::class)]
#[RunTestsInSeparateProcesses]
final class UpdateUserInfoHandlerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $handler = self::getService(UpdateUserInfoHandler::class);
        $this->assertInstanceOf(UpdateUserInfoHandler::class, $handler);
    }

    public function testInvokeWithNonexistentUserReturnsEarly(): void
    {
        $handler = self::getService(UpdateUserInfoHandler::class);
        $message = new UpdateUserInfoMessage(999999, 'test_auth_token');

        // 当用户不存在时，方法应该正常执行而不抛出异常
        $this->expectNotToPerformAssertions();
        $handler->__invoke($message);
    }

    public function testConstructorRequiresDependencies(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(3, $parameters);

        $this->assertSame('userRepository', $parameters[0]->getName());
        $this->assertSame('logger', $parameters[1]->getName());
        $this->assertSame('entityManager', $parameters[2]->getName());
    }

    public function testHandlerHasAsMessageHandlerAttribute(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $attributes = $reflection->getAttributes();

        $hasAsMessageHandlerAttribute = false;
        foreach ($attributes as $attribute) {
            if ('Symfony\Component\Messenger\Attribute\AsMessageHandler' === $attribute->getName()) {
                $hasAsMessageHandlerAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAsMessageHandlerAttribute, 'Handler should have AsMessageHandler attribute');
    }

    public function testInvokeMethodAcceptsUpdateUserInfoMessage(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $invokeMethod = $reflection->getMethod('__invoke');

        $parameters = $invokeMethod->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('message', $parameter->getName());
        $type = $parameter->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame(UpdateUserInfoMessage::class, $type->getName());
    }

    public function testInvokeMethodReturnsVoid(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $invokeMethod = $reflection->getMethod('__invoke');

        $returnType = $invokeMethod->getReturnType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('void', $returnType->getName());
    }

    public function testHandlerClassImplementsCorrectPattern(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $constructor = $reflection->getConstructor();

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $this->assertCount(3, $properties);
    }
}
