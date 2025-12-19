<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class SaveAlipayMiniProgramFormIdParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '小程序ID')]
        public string $miniProgramId = '',

        #[MethodParam(description: '表单ID')]
        public string $formId = '',
    ) {
    }
}
