<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests;

use AlipayMiniProgramBundle\Tests\Utils\MockTokenService;
use Tourze\AccessTokenContracts\TokenServiceInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * Base test case for AlipayMiniProgramBundle integration tests.
 * Automatically registers mock services for testing.
 */
abstract class AlipayMiniProgramKernelTestCase extends AbstractIntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getKernelClass(): string
    {
        return static::guessKernelClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->registerMockTokenService();
    }

    /**
     * Register mock TokenServiceInterface in the test container.
     */
    protected function registerMockTokenService(): void
    {
        $container = self::getContainer();
        $mockService = new MockTokenService();
        $container->set(TokenServiceInterface::class, $mockService);
    }
}
