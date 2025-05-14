<?php

namespace AlipayMiniProgramBundle\Message;

class UpdateUserInfoMessage
{
    public function __construct(
        private readonly int $userId,
        private readonly string $authToken,
    )
    {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }
}
