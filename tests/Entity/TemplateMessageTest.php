<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class TemplateMessageTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $message = new TemplateMessage();
        $miniProgram = new MiniProgram();
        $user = new User();
        $data = ['keyword1' => ['value' => 'test']];
        $sentTime = new \DateTimeImmutable();
        $createTime = new \DateTime();
        $updateTime = new \DateTime();

        // Act & Assert
        $this->assertEquals(0, $message->getId());

        $message->setMiniProgram($miniProgram);
        $this->assertSame($miniProgram, $message->getMiniProgram());

        $message->setToUser($user);
        $this->assertSame($user, $message->getToUser());

        $message->setTemplateId('template_123');
        $this->assertEquals('template_123', $message->getTemplateId());

        $message->setToOpenId('open_id_123');
        $this->assertEquals('open_id_123', $message->getToOpenId());

        $message->setPage('pages/index/index');
        $this->assertEquals('pages/index/index', $message->getPage());

        $message->setData($data);
        $this->assertEquals($data, $message->getData());

        $message->setIsSent(true);
        $this->assertTrue($message->isSent());

        $message->setSentTime($sentTime);
        $this->assertSame($sentTime, $message->getSentTime());

        $message->setSendResult('success');
        $this->assertEquals('success', $message->getSendResult());

        $message->setCreatedBy('admin');
        $this->assertEquals('admin', $message->getCreatedBy());

        $message->setUpdatedBy('admin');
        $this->assertEquals('admin', $message->getUpdatedBy());

        $message->setCreateTime($createTime);
        $this->assertSame($createTime, $message->getCreateTime());

        $message->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $message->getUpdateTime());
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

    public function testSetPage_withNullValue(): void
    {
        // Arrange
        $message = new TemplateMessage();

        // Act
        $result = $message->setPage(null);

        // Assert
        $this->assertSame($message, $result); // Test fluent interface
        $this->assertNull($message->getPage());
    }

    public function testSetSentTime_withNullValue(): void
    {
        // Arrange
        $message = new TemplateMessage();

        // Act
        $result = $message->setSentTime(null);

        // Assert
        $this->assertSame($message, $result); // Test fluent interface
        $this->assertNull($message->getSentTime());
    }

    public function testSetSendResult_withNullValue(): void
    {
        // Arrange
        $message = new TemplateMessage();

        // Act
        $result = $message->setSendResult(null);

        // Assert
        $this->assertSame($message, $result); // Test fluent interface
        $this->assertNull($message->getSendResult());
    }

    public function testSetData_withComplexData(): void
    {
        // Arrange
        $message = new TemplateMessage();
        $complexData = [
            'keyword1' => ['value' => '订单状态'],
            'keyword2' => ['value' => '已发货'],
            'keyword3' => ['value' => '2023-12-01 10:00:00'],
            'remark' => ['value' => '感谢您的购买！']
        ];

        // Act
        $result = $message->setData($complexData);

        // Assert
        $this->assertSame($message, $result); // Test fluent interface
        $this->assertEquals($complexData, $message->getData());
    }

    public function testIsSent_defaultValue(): void
    {
        // Arrange & Act
        $message = new TemplateMessage();

        // Assert
        $this->assertFalse($message->isSent());
    }

    public function testFluentInterface(): void
    {
        // Arrange
        $message = new TemplateMessage();
        $miniProgram = new MiniProgram();
        $user = new User();

        // Act & Assert - Test that all setters return the entity instance
        $this->assertSame($message, $message->setMiniProgram($miniProgram));
        $this->assertSame($message, $message->setToUser($user));
        $this->assertSame($message, $message->setTemplateId('test'));
        $this->assertSame($message, $message->setToOpenId('test'));
        $this->assertSame($message, $message->setPage('test'));
        $this->assertSame($message, $message->setData([]));
        $this->assertSame($message, $message->setIsSent(true));
        $this->assertSame($message, $message->setSentTime(new \DateTimeImmutable()));
        $this->assertSame($message, $message->setSendResult('test'));
        $this->assertSame($message, $message->setCreatedBy('test'));
        $this->assertSame($message, $message->setUpdatedBy('test'));
    }
}
