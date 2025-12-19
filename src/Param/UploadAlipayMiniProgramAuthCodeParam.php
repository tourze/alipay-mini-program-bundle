<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class UploadAlipayMiniProgramAuthCodeParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '小程序的 AppID')]
        public string $appId = '',

        #[MethodParam(description: '授权范围,auth_base 或 auth_user')]
        #[Assert\NotNull]
        public string $scope = '',

        #[MethodParam(description: '授权码')]
        #[Assert\NotNull]
        public string $authCode = '',
    ) {
    }
}
