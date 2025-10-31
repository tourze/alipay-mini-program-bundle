<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Tests\Utils;

use AlipayMiniProgramBundle\Utils\ResponseSanitizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 */
#[CoversClass(ResponseSanitizer::class)]
final class ResponseSanitizerTest extends TestCase
{
    public function testExpectNullableStringWithValidString(): void
    {
        $response = new \stdClass();
        $response->test_key = 'test_value';

        $result = ResponseSanitizer::expectNullableString($response, 'test_key');

        self::assertSame('test_value', $result);
    }

    public function testExpectNullableStringWithNull(): void
    {
        $response = new \stdClass();
        $response->test_key = null;

        $result = ResponseSanitizer::expectNullableString($response, 'test_key');

        self::assertNull($result);
    }

    public function testExpectNullableStringWithMissingProperty(): void
    {
        $response = new \stdClass();

        $result = ResponseSanitizer::expectNullableString($response, 'missing_key');

        self::assertNull($result);
    }

    public function testExpectNullableStringWithInvalidType(): void
    {
        $response = new \stdClass();
        $response->test_key = 123;

        // 捕获 E_USER_WARNING
        set_error_handler(static function (int $errno, string $errstr): bool {
            if (E_USER_WARNING === $errno && str_contains($errstr, 'Expected string|null')) {
                return true; // 抑制错误
            }

            return false;
        });

        $result = ResponseSanitizer::expectNullableString($response, 'test_key');

        restore_error_handler();

        self::assertNull($result);
    }

    public function testExpectNullableIntWithValidInt(): void
    {
        $response = new \stdClass();
        $response->test_key = 42;

        $result = ResponseSanitizer::expectNullableInt($response, 'test_key');

        self::assertSame(42, $result);
    }

    public function testExpectNullableIntWithValidNumericString(): void
    {
        $response = new \stdClass();
        $response->test_key = '123';

        $result = ResponseSanitizer::expectNullableInt($response, 'test_key');

        self::assertSame(123, $result);
    }

    public function testExpectNullableIntWithNull(): void
    {
        $response = new \stdClass();
        $response->test_key = null;

        $result = ResponseSanitizer::expectNullableInt($response, 'test_key');

        self::assertNull($result);
    }

    public function testExpectNullableIntWithMissingProperty(): void
    {
        $response = new \stdClass();

        $result = ResponseSanitizer::expectNullableInt($response, 'missing_key');

        self::assertNull($result);
    }

    public function testExpectNullableIntWithInvalidType(): void
    {
        $response = new \stdClass();
        $response->test_key = 'not_a_number';

        // 捕获 E_USER_WARNING
        set_error_handler(static function (int $errno, string $errstr): bool {
            if (E_USER_WARNING === $errno && str_contains($errstr, 'Expected int|null')) {
                return true; // 抑制错误
            }

            return false;
        });

        $result = ResponseSanitizer::expectNullableInt($response, 'test_key');

        restore_error_handler();

        self::assertNull($result);
    }

    public function testAssertStringOrNullWithValidString(): void
    {
        $response = new \stdClass();
        $response->test_key = 'valid_string';

        // 不应抛出异常
        ResponseSanitizer::assertStringOrNull($response, 'test_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }

    public function testAssertStringOrNullWithNull(): void
    {
        $response = new \stdClass();
        $response->test_key = null;

        // 不应抛出异常
        ResponseSanitizer::assertStringOrNull($response, 'test_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }

    public function testAssertStringOrNullWithMissingProperty(): void
    {
        $response = new \stdClass();

        // 不应抛出异常
        ResponseSanitizer::assertStringOrNull($response, 'missing_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }

    public function testAssertIntOrNullWithValidInt(): void
    {
        $response = new \stdClass();
        $response->test_key = 42;

        // 不应抛出异常
        ResponseSanitizer::assertIntOrNull($response, 'test_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }

    public function testAssertIntOrNullWithValidNumericString(): void
    {
        $response = new \stdClass();
        $response->test_key = '123';

        // 不应抛出异常
        ResponseSanitizer::assertIntOrNull($response, 'test_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }

    public function testAssertIntOrNullWithNull(): void
    {
        $response = new \stdClass();
        $response->test_key = null;

        // 不应抛出异常
        ResponseSanitizer::assertIntOrNull($response, 'test_key');

        self::expectNotToPerformAssertions(); // 不期望异常即通过
    }
}
