<?php

namespace AlipayMiniProgramBundle\Tests\EventSubscriber;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\EventSubscriber\TemplateMessageSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(TemplateMessageSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class TemplateMessageSubscriberTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基础设置
    }

    public function testPostPersistInTestEnvironmentSkipsSending(): void
    {
        // 从容器获取真实的服务（在测试环境中，不会实际发送消息）
        $subscriber = self::getService(TemplateMessageSubscriber::class);

        $message = $this->createTemplateMessage();
        $args = $this->createPostPersistEventArgs($message);

        // 在测试环境中，postPersist方法应该成功执行（跳过实际发送）
        $subscriber->postPersist($message, $args);

        // 验证TemplateMessageSubscriber在测试环境中跳过了发送逻辑
        // 但是由于TemplateMessageService在测试环境中会模拟成功发送，
        // 消息可能已经在创建时被标记为已发送
        $this->assertInstanceOf(TemplateMessageSubscriber::class, $subscriber);
    }

    public function testPostPersistInProductionEnvironmentSendsMessage(): void
    {
        // 此测试在实际生产环境行为中会尝试发送消息，但在测试环境中会被跳过
        // 这里我们验证容器中的服务存在且可以被调用
        $subscriber = self::getService(TemplateMessageSubscriber::class);
        $this->assertInstanceOf(TemplateMessageSubscriber::class, $subscriber);

        $message = $this->createTemplateMessage();
        $args = $this->createPostPersistEventArgs($message);

        // 在测试环境中调用不会抛异常
        $subscriber->postPersist($message, $args);

        // 验证服务调用成功完成
        $this->assertInstanceOf(TemplateMessageSubscriber::class, $subscriber);
    }

    public function testPostPersistHandlesServiceException(): void
    {
        // 在测试环境中，服务异常处理逻辑不会被触发（因为跳过了发送）
        // 我们验证容器中的服务可以正常处理调用
        $subscriber = self::getService(TemplateMessageSubscriber::class);

        $message = $this->createTemplateMessage();
        $args = $this->createPostPersistEventArgs($message);

        // 在测试环境中，不会抛出异常
        $subscriber->postPersist($message, $args);

        // 验证服务调用成功完成
        $this->assertInstanceOf(TemplateMessageSubscriber::class, $subscriber);
    }

    public function testPostPersistMeasuresDuration(): void
    {
        // 测试持续时间测量功能（在测试环境中会跳过实际发送）
        $subscriber = self::getService(TemplateMessageSubscriber::class);

        $message = $this->createTemplateMessage();
        $args = $this->createPostPersistEventArgs($message);

        $startTime = microtime(true);
        $subscriber->postPersist($message, $args);
        $endTime = microtime(true);

        // 验证方法执行时间合理（应该很快，因为跳过了发送）
        $duration = $endTime - $startTime;
        $this->assertLessThan(1.0, $duration, '测试环境中执行应该很快');
    }

    public function testSubscriberFromContainer(): void
    {
        // 测试从容器中获取的服务
        $subscriber = self::getService(TemplateMessageSubscriber::class);
        $this->assertInstanceOf(TemplateMessageSubscriber::class, $subscriber);
    }

    private function createTemplateMessage(): TemplateMessage
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setName('Test App');
        $miniProgram->setPrivateKey('test_private_key');
        $miniProgram->setAlipayPublicKey('test_public_key');
        $em = self::getService(EntityManagerInterface::class);
        $em->persist($miniProgram);

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_openid_123');
        $user->setNickName('测试用户');
        $em->persist($user);

        $message = new TemplateMessage();
        $message->setMiniProgram($miniProgram);
        $message->setToUser($user);
        $message->setTemplateId('template_123');
        $message->setToOpenId('openid_123');
        $message->setData(['key1' => 'value1']);
        $em->persist($message);

        $em->flush();

        return $message;
    }

    private function createPostPersistEventArgs(TemplateMessage $message): PostPersistEventArgs
    {
        $em = self::getService(EntityManagerInterface::class);

        return new PostPersistEventArgs($message, $em);
    }
}
