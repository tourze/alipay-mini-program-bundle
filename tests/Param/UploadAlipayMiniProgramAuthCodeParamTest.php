<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Param;

use AlipayMiniProgramBundle\Param\UploadAlipayMiniProgramAuthCodeParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(UploadAlipayMiniProgramAuthCodeParam::class)]
final class UploadAlipayMiniProgramAuthCodeParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new UploadAlipayMiniProgramAuthCodeParam(
            appId: 'test-app-id',
            scope: 'auth_base',
            authCode: 'test-auth-code',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-app-id', $param->appId);
        $this->assertSame('auth_base', $param->scope);
        $this->assertSame('test-auth-code', $param->authCode);
    }

    public function testParamIsReadonly(): void
    {
        $param = new UploadAlipayMiniProgramAuthCodeParam(
            appId: 'test-app-id-2',
            scope: 'auth_user',
            authCode: 'test-auth-code-2',
        );

        $this->assertSame('test-app-id-2', $param->appId);
        $this->assertSame('auth_user', $param->scope);
        $this->assertSame('test-auth-code-2', $param->authCode);
    }
}
