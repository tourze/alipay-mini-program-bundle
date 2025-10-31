<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Utils;

/**
 * 响应数据清理器，用于从 stdClass 到类型化属性的类型安全转换。
 *
 * 为支付宝 API 响应提供类型安全的提取方法，确保 PHPStan level=max 兼容性，
 * 同时保持业务逻辑兼容性。
 */
final class ResponseSanitizer
{
    /**
     * 提取字符串值，对于缺失或非字符串属性返回 null。
     */
    public static function expectNullableString(\stdClass $response, string $key): ?string
    {
        // Convert stdClass to array to avoid dynamic property access
        $responseArray = (array) $response;

        if (!array_key_exists($key, $responseArray)) {
            return null;
        }

        $value = $responseArray[$key];

        if (null === $value) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        // Log unexpected type for observability but don't break business logic
        trigger_error(
            sprintf('Expected string|null for %s, got %s', $key, get_debug_type($value)),
            \E_USER_WARNING
        );

        return null;
    }

    /**
     * 提取整数值，对于缺失或非数值属性返回 null。
     */
    public static function expectNullableInt(\stdClass $response, string $key): ?int
    {
        // Convert stdClass to array to avoid dynamic property access
        $responseArray = (array) $response;

        if (!array_key_exists($key, $responseArray)) {
            return null;
        }

        $value = $responseArray[$key];

        if (null === $value) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        // Log unexpected type for observability
        trigger_error(
            sprintf('Expected int|null for %s, got %s', $key, get_debug_type($value)),
            \E_USER_WARNING
        );

        return null;
    }

    /**
     * 检查响应属性是否存在并具有预期类型，用于 PHPStan 断言。
     */
    public static function assertStringOrNull(\stdClass $response, string $key): void
    {
        // Convert stdClass to array to avoid dynamic property access
        $responseArray = (array) $response;

        if (!array_key_exists($key, $responseArray)) {
            return;
        }

        $value = $responseArray[$key];
        assert(is_string($value) || null === $value, sprintf('Property %s must be string|null', $key));
    }

    /**
     * 检查响应属性是否存在并具有预期类型，用于 PHPStan 断言。
     */
    public static function assertIntOrNull(\stdClass $response, string $key): void
    {
        // Convert stdClass to array to avoid dynamic property access
        $responseArray = (array) $response;

        if (!array_key_exists($key, $responseArray)) {
            return;
        }

        $value = $responseArray[$key];
        assert(is_int($value) || is_numeric($value) || null === $value, sprintf('Property %s must be int|numeric|null', $key));
    }
}
