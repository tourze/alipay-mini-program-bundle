<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(TemplateMessage::class)]
final class TemplateMessageTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        // TemplateMessage has required relations, so we need to create a minimal valid entity
        $miniProgram = new MiniProgram();
        $miniProgram->setName('Test MiniProgram');
        $miniProgram->setAppId('test_app_id');
        $miniProgram->setPrivateKey('test_key');
        $miniProgram->setAlipayPublicKey('test_public_key');

        $user = new User();
        $user->setMiniProgram($miniProgram);
        $user->setOpenId('test_open_id');

        $message = new TemplateMessage();
        $message->setMiniProgram($miniProgram);
        $message->setToUser($user);
        $message->setTemplateId('test_template');
        $message->setToOpenId('test_open_id');

        return $message;
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'templateId' => ['templateId', 'template_123'],
            'toOpenId' => ['toOpenId', 'open_id_123'],
            'page' => ['page', 'pages/index/index'],
            'data' => ['data', ['keyword1' => ['value' => 'test']]],
            'sentTime' => ['sentTime', new \DateTimeImmutable()],
            'sendResult' => ['sendResult', 'success'],
        ];
    }

    public function testDefaultValues(): void
    {
        // Arrange & Act
        $message = new TemplateMessage();

        // Assert
        $this->assertFalse($message->isSent());
        $this->assertEquals([], $message->getData());
        $this->assertNull($message->getPage());
        $this->assertNull($message->getSentTime());
        $this->assertNull($message->getSendResult());
    }

    public function testSetDataWithComplexData(): void
    {
        // Arrange
        $message = new TemplateMessage();
        $complexData = [
            'keyword1' => ['value' => '订单状态'],
            'keyword2' => ['value' => '已发货'],
            'keyword3' => ['value' => '2023-12-01 10:00:00'],
            'remark' => ['value' => '感谢您的购买！'],
        ];

        // Act
        $message->setData($complexData);

        // Assert

        $this->assertEquals($complexData, $message->getData());
    }

    public function testIsSentGetterSetter(): void
    {
        // Arrange
        $message = $this->createEntity();
        $this->assertInstanceOf(TemplateMessage::class, $message);

        // Act & Assert - Test fluent interface
        $message->setIsSent(true);
        $this->assertTrue($message->isSent());

        $message->setIsSent(false);
        $this->assertFalse($message->isSent());
    }
}
