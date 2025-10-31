<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Exception;

use AlipayMiniProgramBundle\Exception\UserNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UserNotFoundException::class)]
final class UserNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testDefaultMessage(): void
    {
        $exception = new UserNotFoundException();

        $this->assertEquals('用户不存在', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testCustomMessage(): void
    {
        $message = '自定义用户不存在消息';
        $exception = new UserNotFoundException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testCustomMessageAndCode(): void
    {
        $message = '自定义用户不存在消息';
        $code = 404;
        $exception = new UserNotFoundException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testWithPreviousException(): void
    {
        $previous = new \RuntimeException('前一个异常');
        $exception = new UserNotFoundException('用户不存在', 0, $previous);

        $this->assertEquals('用户不存在', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testIsInstanceOfRuntimeException(): void
    {
        $exception = new UserNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
