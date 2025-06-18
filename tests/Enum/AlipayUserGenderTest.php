<?php

namespace AlipayMiniProgramBundle\Tests\Enum;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use PHPUnit\Framework\TestCase;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;

class AlipayUserGenderTest extends TestCase
{
    public function test_enum_values_are_correct(): void
    {
        $this->assertSame('F', AlipayUserGender::FEMALE->value);
        $this->assertSame('M', AlipayUserGender::MALE->value);
    }

    public function test_enum_labels_are_correct(): void
    {
        $this->assertSame('女', AlipayUserGender::FEMALE->getLabel());
        $this->assertSame('男', AlipayUserGender::MALE->getLabel());
    }

    public function test_enum_implements_required_interfaces(): void
    {
        $reflection = new \ReflectionClass(AlipayUserGender::class);
        
        $this->assertTrue($reflection->implementsInterface(Labelable::class));
        $this->assertTrue($reflection->implementsInterface(Itemable::class));
        $this->assertTrue($reflection->implementsInterface(Selectable::class));
    }

    public function test_from_string_creates_correct_enum(): void
    {
        $this->assertSame(AlipayUserGender::FEMALE, AlipayUserGender::from('F'));
        $this->assertSame(AlipayUserGender::MALE, AlipayUserGender::from('M'));
    }

    public function test_from_string_throws_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);
        AlipayUserGender::from('INVALID');
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(AlipayUserGender::tryFrom('INVALID'));
        $this->assertNull(AlipayUserGender::tryFrom(''));
        $this->assertNull(AlipayUserGender::tryFrom('X'));
    }

    public function test_try_from_returns_correct_enum_for_valid_value(): void
    {
        $this->assertSame(AlipayUserGender::FEMALE, AlipayUserGender::tryFrom('F'));
        $this->assertSame(AlipayUserGender::MALE, AlipayUserGender::tryFrom('M'));
    }

    public function test_cases_returns_all_enum_values(): void
    {
        $cases = AlipayUserGender::cases();
        
        $this->assertCount(2, $cases);
        $this->assertContains(AlipayUserGender::FEMALE, $cases);
        $this->assertContains(AlipayUserGender::MALE, $cases);
    }

    public function test_enum_values_are_unique(): void
    {
        $values = array_map(fn($case) => $case->value, AlipayUserGender::cases());
        $uniqueValues = array_unique($values);
        
        $this->assertSame(count($values), count($uniqueValues));
    }

    public function test_enum_labels_are_unique(): void
    {
        $labels = array_map(fn($case) => $case->getLabel(), AlipayUserGender::cases());
        $uniqueLabels = array_unique($labels);
        
        $this->assertSame(count($labels), count($uniqueLabels));
    }

    public function test_to_string_returns_enum_name(): void
    {
        $this->assertSame('FEMALE', AlipayUserGender::FEMALE->name);
        $this->assertSame('MALE', AlipayUserGender::MALE->name);
    }

    public function test_to_select_item_trait_method(): void
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

    public function test_gen_options_trait_method(): void
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
} 