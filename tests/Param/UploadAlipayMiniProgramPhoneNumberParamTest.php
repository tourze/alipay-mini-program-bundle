<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Param;

use AlipayMiniProgramBundle\Param\UploadAlipayMiniProgramPhoneNumberParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(UploadAlipayMiniProgramPhoneNumberParam::class)]
final class UploadAlipayMiniProgramPhoneNumberParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new UploadAlipayMiniProgramPhoneNumberParam(
            encryptedData: 'test-encrypted-data',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-encrypted-data', $param->encryptedData);
    }

    public function testParamIsReadonly(): void
    {
        $param = new UploadAlipayMiniProgramPhoneNumberParam(
            encryptedData: 'test-encrypted-data-2',
        );

        $this->assertSame('test-encrypted-data-2', $param->encryptedData);
    }
}
