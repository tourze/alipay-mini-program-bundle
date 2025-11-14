<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Utils;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\AccessTokenBundle\Entity\AccessToken;
use Tourze\AccessTokenContracts\AccessTokenInterface;
use Tourze\AccessTokenContracts\TokenServiceInterface;

/**
 * Mock implementation of TokenServiceInterface for testing purposes.
 */
final class MockTokenService implements TokenServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createToken(
        UserInterface $user,
        ?int $expiresIn = null,
        ?string $deviceInfo = null
    ): AccessTokenInterface {
        // Return a mock AccessToken for testing
        // This creates an in-memory representation without persisting to database
        $token = AccessToken::create(
            user: $user,
            expiresIn: $expiresIn ?? 86400, // Default 1 day
            deviceInfo: $deviceInfo
        );

        return $token;
    }
}
