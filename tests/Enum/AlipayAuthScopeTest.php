<?php

namespace AlipayMiniProgramBundle\Tests\Enum;

use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use PHPUnit\Framework\TestCase;

class AlipayAuthScopeTest extends TestCase
{
    public function testEnumValues(): void
    {
        // Test that enum has correct values
        $this->assertEquals('auth_base', AlipayAuthScope::AUTH_BASE->value);
        $this->assertEquals('auth_user', AlipayAuthScope::AUTH_USER->value);
    }
    
    public function testGetLabel(): void
    {
        // Test AUTH_BASE label
        $this->assertEquals('基础授权', AlipayAuthScope::AUTH_BASE->getLabel());
        
        // Test AUTH_USER label
        $this->assertEquals('用户信息授权', AlipayAuthScope::AUTH_USER->getLabel());
    }
    
    public function testFromValue(): void
    {
        // Test creating enum from string values
        $authBase = AlipayAuthScope::from('auth_base');
        $this->assertSame(AlipayAuthScope::AUTH_BASE, $authBase);
        
        $authUser = AlipayAuthScope::from('auth_user');
        $this->assertSame(AlipayAuthScope::AUTH_USER, $authUser);
    }
    
    public function testFromValue_withInvalidValue(): void
    {
        // Test that invalid value throws exception
        $this->expectException(\ValueError::class);
        AlipayAuthScope::from('invalid_scope');
    }
    
    public function testTryFromValue(): void
    {
        // Test valid values
        $authBase = AlipayAuthScope::tryFrom('auth_base');
        $this->assertSame(AlipayAuthScope::AUTH_BASE, $authBase);
        
        $authUser = AlipayAuthScope::tryFrom('auth_user');
        $this->assertSame(AlipayAuthScope::AUTH_USER, $authUser);
        
        // Test invalid value returns null
        $invalid = AlipayAuthScope::tryFrom('invalid_scope');
        $this->assertNull($invalid);
    }
    
    public function testCases(): void
    {
        // Test that cases() returns all enum values
        $cases = AlipayAuthScope::cases();
        
        $this->assertCount(2, $cases);
        $this->assertContains(AlipayAuthScope::AUTH_BASE, $cases);
        $this->assertContains(AlipayAuthScope::AUTH_USER, $cases);
    }
    
    public function testEnumImplementsInterfaces(): void
    {
        // Test that enum implements required interfaces
        $authBase = AlipayAuthScope::AUTH_BASE;
        
        $this->assertInstanceOf(\Tourze\EnumExtra\Labelable::class, $authBase);
        $this->assertInstanceOf(\Tourze\EnumExtra\Itemable::class, $authBase);
        $this->assertInstanceOf(\Tourze\EnumExtra\Selectable::class, $authBase);
    }
    
    public function testAllLabelsAreUnique(): void
    {
        // Test that all labels are different
        $labels = [];
        foreach (AlipayAuthScope::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotContains($label, $labels, 'Label should be unique: ' . $label);
            $labels[] = $label;
        }
    }
    
    public function testAllValuesAreUnique(): void
    {
        // Test that all values are different
        $values = [];
        foreach (AlipayAuthScope::cases() as $case) {
            $value = $case->value;
            $this->assertNotContains($value, $values, 'Value should be unique: ' . $value);
            $values[] = $value;
        }
    }
} 