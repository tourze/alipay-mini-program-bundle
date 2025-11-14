<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Utils;

use Tourze\AccessTokenContracts\TokenServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Trait to register mock TokenServiceInterface in test containers.
 *
 * Usage:
 * ```php
 * class SomeTest extends KernelTestCase
 * {
 *     use RegisterMockTokenServiceTrait;
 *
 *     protected function setUp(): void
 *     {
 *         parent::setUp();
 *         $this->registerMockTokenService();
 *     }
 * }
 * ```
 */
trait RegisterMockTokenServiceTrait
{
    /**
     * Register mock TokenServiceInterface in the test container.
     */
    protected function registerMockTokenService(): void
    {
        $container = static::getContainer();
        $mockService = new MockTokenService();
        $container->set(TokenServiceInterface::class, $mockService);
    }
}
