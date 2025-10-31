<?php

namespace AlipayMiniProgramBundle\Exception;

/**
 * 当小程序配置不完整或无效时抛出的异常
 */
class InvalidMiniProgramConfigurationException extends \RuntimeException
{
    public function __construct(string $message = 'MiniProgram configuration is incomplete', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
