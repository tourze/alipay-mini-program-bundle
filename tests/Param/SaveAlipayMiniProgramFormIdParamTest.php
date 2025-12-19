<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Param;

use AlipayMiniProgramBundle\Param\SaveAlipayMiniProgramFormIdParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(SaveAlipayMiniProgramFormIdParam::class)]
final class SaveAlipayMiniProgramFormIdParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new SaveAlipayMiniProgramFormIdParam(
            miniProgramId: 'test-mini-program-id',
            formId: 'test-form-id',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
        $this->assertSame('test-mini-program-id', $param->miniProgramId);
        $this->assertSame('test-form-id', $param->formId);
    }

    public function testParamIsReadonly(): void
    {
        $param = new SaveAlipayMiniProgramFormIdParam(
            miniProgramId: 'test-mini-program-id-2',
            formId: 'test-form-id-2',
        );

        $this->assertSame('test-mini-program-id-2', $param->miniProgramId);
        $this->assertSame('test-form-id-2', $param->formId);
    }
}
