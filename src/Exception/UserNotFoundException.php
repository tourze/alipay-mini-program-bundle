<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Exception;

/**
 * 当找不到用户时抛出的异常
 */
class UserNotFoundException extends \RuntimeException
{
    public function __construct(string $message = '用户不存在', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
