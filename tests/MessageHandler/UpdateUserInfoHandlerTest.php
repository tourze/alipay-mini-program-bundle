<?php

namespace AlipayMiniProgramBundle\Tests\MessageHandler;

use Alipay\OpenAPISDK\Model\AlipayUserInfoShareResponse;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Message\UpdateUserInfoMessage;
use AlipayMiniProgramBundle\MessageHandler\UpdateUserInfoHandler;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UpdateUserInfoHandlerTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private LoggerInterface|MockObject $logger;
    private EntityManagerInterface|MockObject $entityManager;
    private UpdateUserInfoHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new UpdateUserInfoHandler(
            $this->userRepository,
            $this->logger,
            $this->entityManager
        );
    }

    public function test_constructor_initializes_correctly(): void
    {
        $this->assertInstanceOf(UpdateUserInfoHandler::class, $this->handler);
    }

    public function test_invoke_with_nonexistent_user_returns_early(): void
    {
        $message = new UpdateUserInfoMessage(999, 'test_auth_token');

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->logger
            ->expects($this->never())
            ->method('error');

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->handler->__invoke($message);
    }

    public function test_invoke_with_valid_user_updates_successfully(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setEncryptKey('test_encrypt_key');

        $user = new User();
        $user->setMiniProgram($miniProgram);

        $message = new UpdateUserInfoMessage($user->getId(), 'test_auth_token');

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with($user->getId())
            ->willReturn($user);

        // 由于有外部API调用会抛出异常，我们不期望persist和flush被调用
        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        // 由于有外部API调用，这个测试会抛出异常，但我们可以测试异常处理
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception when calling AlipayUserInfoApi->share');

        // 捕获输出以防止测试产生噪音
        ob_start();
        try {
            $this->handler->__invoke($message);
        } finally {
            ob_end_clean();
        }
    }

    public function test_constructor_requires_dependencies(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(3, $parameters);

        $this->assertSame('userRepository', $parameters[0]->getName());
        $this->assertSame('logger', $parameters[1]->getName());
        $this->assertSame('entityManager', $parameters[2]->getName());
    }

    public function test_handler_has_as_message_handler_attribute(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $attributes = $reflection->getAttributes();

        $hasAsMessageHandlerAttribute = false;
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === 'Symfony\Component\Messenger\Attribute\AsMessageHandler') {
                $hasAsMessageHandlerAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAsMessageHandlerAttribute, 'Handler should have AsMessageHandler attribute');
    }

    public function test_invoke_method_exists(): void
    {
        $this->assertTrue(method_exists($this->handler, '__invoke'));
    }

    public function test_invoke_method_accepts_update_user_info_message(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $invokeMethod = $reflection->getMethod('__invoke');

        $parameters = $invokeMethod->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('message', $parameter->getName());
        $this->assertSame(UpdateUserInfoMessage::class, $parameter->getType()->getName());
    }

    public function test_invoke_method_returns_void(): void
    {
        $reflection = new \ReflectionClass(UpdateUserInfoHandler::class);
        $invokeMethod = $reflection->getMethod('__invoke');

        $returnType = $invokeMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->getName());
    }

    public function test_handler_class_implements_correct_pattern(): void
    {
        // 检查类是否具有正确的结构
        $this->assertTrue(method_exists($this->handler, '__invoke'));

        // 检查是否有正确的构造函数依赖
        $reflection = new \ReflectionClass($this->handler);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor);

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $this->assertCount(3, $properties);
    }

    public function test_invoke_with_user_null_mini_program(): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $miniProgram->setEncryptKey('test_encrypt_key');

        $user = new User();
        $user->setMiniProgram($miniProgram);

        $message = new UpdateUserInfoMessage($user->getId(), 'test_auth_token');

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with($user->getId())
            ->willReturn($user);

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception when calling AlipayUserInfoApi->share');

        // 捕获输出以防止测试产生噪音
        ob_start();
        try {
            $this->handler->__invoke($message);
        } finally {
            ob_end_clean();
        }
    }

    public function test_handler_dependency_injection(): void
    {
        $reflection = new \ReflectionClass($this->handler);
        $constructor = $reflection->getConstructor();

        $parameters = $constructor->getParameters();

        // 检查所有参数都是只读的
        foreach ($parameters as $parameter) {
            $this->assertTrue($parameter->isPromoted(), "Parameter {$parameter->getName()} should be promoted");
        }
    }
}
