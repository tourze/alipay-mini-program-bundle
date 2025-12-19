<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class UploadAlipayMiniProgramPhoneNumberParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '加密数据')]
        #[Assert\NotNull]
        public string $encryptedData = '',
    ) {
    }
}
