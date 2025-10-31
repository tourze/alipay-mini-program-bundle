<?php

namespace AlipayMiniProgramBundle\Tests\Entity;

use AlipayMiniProgramBundle\Entity\FormId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(FormId::class)]
final class FormIdTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new FormId();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $now = new \DateTimeImmutable();

        return [
            'formId' => ['formId', 'test_form_id'],
            'expireTime' => ['expireTime', $now],
            'usedCount' => ['usedCount', 2],
        ];
    }

    public function testIncrementUsedCount(): void
    {
        // Arrange
        $formId = new FormId();
        $formId->setUsedCount(2);

        // Act
        $formId->incrementUsedCount();

        // Assert

        $this->assertEquals(3, $formId->getUsedCount());
    }

    public function testIsUsed(): void
    {
        // Arrange
        $formId = new FormId();

        // Act & Assert - Test when usedCount >= 3
        $formId->setUsedCount(3);
        $this->assertTrue($formId->isUsed());

        // Act & Assert - Test when usedCount < 3
        $formId->setUsedCount(2);
        $this->assertFalse($formId->isUsed());
    }
}
