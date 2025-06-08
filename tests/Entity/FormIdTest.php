<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\FormId;
use PHPUnit\Framework\TestCase;

class FormIdTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Arrange
        $formId = new FormId();
        $now = new \DateTime();

        // Act & Assert
        $this->assertEquals(0, $formId->getId());

        $formId->setFormId('test_form_id');
        $this->assertEquals('test_form_id', $formId->getFormId());

        $formId->setExpireTime($now);
        $this->assertSame($now, $formId->getExpireTime());

        $formId->setUsedCount(2);
        $this->assertEquals(2, $formId->getUsedCount());

        $formId->incrementUsedCount();
        $this->assertEquals(3, $formId->getUsedCount());

        $this->assertTrue($formId->isUsed());

        $formId->setUsedCount(1);
        $this->assertFalse($formId->isUsed());
    }
}
