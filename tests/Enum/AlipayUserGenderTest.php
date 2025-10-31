<?php

namespace AlipayMiniProgramBundle\Tests\Enum;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayUserGender::class)]
final class AlipayUserGenderTest extends AbstractEnumTestCase
{
    public function testEnumValuesAreCorrect(): void
    {
        $this->assertSame('F', AlipayUserGender::FEMALE->value);
        $this->assertSame('M', AlipayUserGender::MALE->value);
    }

    public function testEnumLabelsAreCorrect(): void
    {
        $this->assertSame('女', AlipayUserGender::FEMALE->getLabel());
        $this->assertSame('男', AlipayUserGender::MALE->getLabel());
    }

    public function testEnumImplementsRequiredInterfaces(): void
    {
        $reflection = new \ReflectionClass(AlipayUserGender::class);

        $this->assertTrue($reflection->implementsInterface(Labelable::class));
        $this->assertTrue($reflection->implementsInterface(Itemable::class));
        $this->assertTrue($reflection->implementsInterface(Selectable::class));
    }

    public function testFromStringCreatesCorrectEnum(): void
    {
        $this->assertSame(AlipayUserGender::FEMALE, AlipayUserGender::from('F'));
        $this->assertSame(AlipayUserGender::MALE, AlipayUserGender::from('M'));
    }

    public function testFromStringThrowsForInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        AlipayUserGender::from('INVALID');
    }

    public function testTryFromReturnsNullForInvalidValue(): void
    {
        $this->assertNull(AlipayUserGender::tryFrom('INVALID'));
        $this->assertNull(AlipayUserGender::tryFrom(''));
        $this->assertNull(AlipayUserGender::tryFrom('X'));
    }

    public function testTryFromReturnsCorrectEnumForValidValue(): void
    {
        $this->assertSame(AlipayUserGender::FEMALE, AlipayUserGender::tryFrom('F'));
        $this->assertSame(AlipayUserGender::MALE, AlipayUserGender::tryFrom('M'));
    }

    public function testCasesReturnsAllEnumValues(): void
    {
        $cases = AlipayUserGender::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(AlipayUserGender::FEMALE, $cases);
        $this->assertContains(AlipayUserGender::MALE, $cases);
    }

    public function testEnumValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, AlipayUserGender::cases());
        $uniqueValues = array_unique($values);

        $this->assertSame(count($values), count($uniqueValues));
    }

    public function testEnumLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), AlipayUserGender::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertSame(count($labels), count($uniqueLabels));
    }

    public function testToStringReturnsEnumName(): void
    {
        $this->assertSame('FEMALE', AlipayUserGender::FEMALE->name);
        $this->assertSame('MALE', AlipayUserGender::MALE->name);
    }

    public function testToSelectItemTraitMethod(): void
    {
        $femaleItem = AlipayUserGender::FEMALE->toSelectItem();
        $this->assertArrayHasKey('value', $femaleItem);
        $this->assertArrayHasKey('label', $femaleItem);
        $this->assertArrayHasKey('text', $femaleItem);
        $this->assertArrayHasKey('name', $femaleItem);
        $this->assertSame('F', $femaleItem['value']);
        $this->assertSame('女', $femaleItem['label']);
        $this->assertSame('女', $femaleItem['text']);
        $this->assertSame('女', $femaleItem['name']);
    }

    public function testGenOptionsTraitMethod(): void
    {
        $options = AlipayUserGender::genOptions();
        $this->assertCount(2, $options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('text', $option);
            $this->assertArrayHasKey('name', $option);
        }
    }

    public function testToArray(): void
    {
        // Test FEMALE toArray()
        $femaleArray = AlipayUserGender::FEMALE->toArray();
        // toArray() always returns array, verify content instead
        $this->assertArrayHasKey('value', $femaleArray);
        $this->assertArrayHasKey('label', $femaleArray);
        $this->assertEquals('F', $femaleArray['value']);
        $this->assertEquals('女', $femaleArray['label']);

        // Test MALE toArray()
        $maleArray = AlipayUserGender::MALE->toArray();
        // toArray() always returns array, verify content instead
        $this->assertArrayHasKey('value', $maleArray);
        $this->assertArrayHasKey('label', $maleArray);
        $this->assertEquals('M', $maleArray['value']);
        $this->assertEquals('男', $maleArray['label']);

        // Test toArray structure consistency
        foreach (AlipayUserGender::cases() as $case) {
            $array = $case->toArray();
            // toArray() always returns array, verify content instead
            $this->assertCount(2, $array); // Should have exactly 2 elements
            $this->assertArrayHasKey('value', $array);
            $this->assertArrayHasKey('label', $array);
            $this->assertEquals($case->value, $array['value']);
            $this->assertEquals($case->getLabel(), $array['label']);
        }
    }
}
