<?php

namespace AlipayMiniProgramBundle\Tests\Response;

use AlipayMiniProgramBundle\Response\AlipaySystemOauthTokenResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AlipaySystemOauthTokenResponse::class)]
final class AlipaySystemOauthTokenResponseTest extends TestCase
{
    public function testConstructorWithSuccessfulResponse(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'msg' => 'Success',
            'user_id' => 'test_user_123',
            'open_id' => 'test_open_id_456',
            'access_token' => 'test_access_token_789',
            'refresh_token' => 'test_refresh_token_abc',
            'expires_in' => '3600',
            're_expires_in' => '7200',
            'auth_start' => '2023-01-01 00:00:00',
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('10000', $response->getCode());
        $this->assertSame('Success', $response->getMsg());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('test_open_id_456', $response->getOpenId());
        $this->assertSame('test_access_token_789', $response->getAccessToken());
        $this->assertSame('test_refresh_token_abc', $response->getRefreshToken());
        $this->assertSame(3600, $response->getExpiresIn());
        $this->assertSame(7200, $response->getReExpiresIn());
        $this->assertSame('2023-01-01 00:00:00', $response->getAuthStart());
    }

    public function testConstructorWithFailedResponse(): void
    {
        $responseData = (object) [
            'code' => '40001',
            'msg' => 'Missing Required Arguments',
            'sub_code' => 'isv.missing-signature',
            'sub_msg' => '缺少签名参数',
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertFalse($response->isSuccess());
        $this->assertSame('40001', $response->getCode());
        $this->assertSame('Missing Required Arguments', $response->getMsg());
        $this->assertSame('isv.missing-signature', $response->getSubCode());
        $this->assertSame('缺少签名参数', $response->getSubMsg());
        $this->assertNull($response->getUserId());
        $this->assertNull($response->getOpenId());
        $this->assertNull($response->getAccessToken());
        $this->assertNull($response->getRefreshToken());
        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getReExpiresIn());
        $this->assertNull($response->getAuthStart());
    }

    public function testConstructorWithEmptyResponse(): void
    {
        $responseData = new \stdClass();

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess()); // 空code被认为是成功
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMsg());
        $this->assertNull($response->getSubCode());
        $this->assertNull($response->getSubMsg());
        $this->assertNull($response->getUserId());
        $this->assertNull($response->getOpenId());
        $this->assertNull($response->getAccessToken());
        $this->assertNull($response->getRefreshToken());
        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getReExpiresIn());
        $this->assertNull($response->getAuthStart());
    }

    public function testConstructorWithIntegerExpiresIn(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'expires_in' => 3600,
            're_expires_in' => 7200,
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertSame(3600, $response->getExpiresIn());
        $this->assertSame(7200, $response->getReExpiresIn());
    }

    public function testConstructorWithStringExpiresIn(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'expires_in' => '3600',
            're_expires_in' => '7200',
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertSame(3600, $response->getExpiresIn());
        $this->assertSame(7200, $response->getReExpiresIn());
    }

    public function testConstructorWithNullExpiresIn(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'expires_in' => null,
            're_expires_in' => null,
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getReExpiresIn());
    }

    public function testIsSuccessWithNullCode(): void
    {
        $responseData = new \stdClass();

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessWithSuccessCode(): void
    {
        $responseData = (object) ['code' => '10000'];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessWithErrorCode(): void
    {
        $responseData = (object) ['code' => '40001'];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertFalse($response->isSuccess());
    }

    public function testConstructorWithPartialData(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123',
            'access_token' => 'test_access_token_789',
            // 缺少其他字段
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('test_access_token_789', $response->getAccessToken());
        $this->assertNull($response->getOpenId());
        $this->assertNull($response->getRefreshToken());
        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getReExpiresIn());
        $this->assertNull($response->getAuthStart());
    }

    public function testAllGetterMethodsExist(): void
    {
        $methods = [
            'isSuccess', 'getCode', 'getMsg', 'getSubCode', 'getSubMsg',
            'getUserId', 'getOpenId', 'getAccessToken', 'getRefreshToken',
            'getExpiresIn', 'getReExpiresIn', 'getAuthStart',
        ];

        foreach ($methods as $method) {
            // Verify all required methods exist by creating instance and checking method
            $instance = new AlipaySystemOauthTokenResponse((object) ['test' => 'response']);
            $this->assertTrue(
                method_exists($instance, $method),
                "Method {$method} should exist on instance"
            );
        }
    }

    public function testConstructorWithAllFields(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'msg' => 'Success',
            'sub_code' => 'test_sub_code',
            'sub_msg' => 'test_sub_msg',
            'user_id' => 'test_user_123',
            'open_id' => 'test_open_id_456',
            'access_token' => 'test_access_token_789',
            'refresh_token' => 'test_refresh_token_abc',
            'expires_in' => '3600',
            're_expires_in' => '7200',
            'auth_start' => '2023-01-01 00:00:00',
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('10000', $response->getCode());
        $this->assertSame('Success', $response->getMsg());
        $this->assertSame('test_sub_code', $response->getSubCode());
        $this->assertSame('test_sub_msg', $response->getSubMsg());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('test_open_id_456', $response->getOpenId());
        $this->assertSame('test_access_token_789', $response->getAccessToken());
        $this->assertSame('test_refresh_token_abc', $response->getRefreshToken());
        $this->assertSame(3600, $response->getExpiresIn());
        $this->assertSame(7200, $response->getReExpiresIn());
        $this->assertSame('2023-01-01 00:00:00', $response->getAuthStart());
    }

    public function testConstructorAcceptsStdclass(): void
    {
        $responseData = new \stdClass();
        $responseData->code = '10000';

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testResponseImmutability(): void
    {
        $responseData = (object) ['code' => '10000'];
        $response = new AlipaySystemOauthTokenResponse($responseData);

        // 验证没有setter方法
        $reflection = new \ReflectionClass($response);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $setterMethods = array_filter($methods, function (\ReflectionMethod $method) {
            return str_starts_with($method->getName(), 'set');
        });

        $this->assertEmpty($setterMethods, 'Response should be immutable - no setter methods allowed');
    }

    public function testConstructorParameterType(): void
    {
        $reflection = new \ReflectionClass(AlipaySystemOauthTokenResponse::class);
        $constructor = $reflection->getConstructor();
        $this->assertNotNull($constructor, 'Constructor must exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('response', $parameter->getName());
        $type = $parameter->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame('stdClass', $type->getName());
    }

    public function testExpiresInConversionEdgeCases(): void
    {
        // 测试零值 - 由于代码逻辑，0会被认为是falsy，返回null
        $zeroResponse = (object) ['expires_in' => '0', 're_expires_in' => '0'];
        $zeroResult = new AlipaySystemOauthTokenResponse($zeroResponse);
        $this->assertNull($zeroResult->getExpiresIn());
        $this->assertNull($zeroResult->getReExpiresIn());

        // 测试负值
        $negativeResponse = (object) ['expires_in' => '-1', 're_expires_in' => '-1'];
        $negativeResult = new AlipaySystemOauthTokenResponse($negativeResponse);
        $this->assertSame(-1, $negativeResult->getExpiresIn());
        $this->assertSame(-1, $negativeResult->getReExpiresIn());

        // 测试大数值
        $largeResponse = (object) ['expires_in' => '999999999', 're_expires_in' => '999999999'];
        $largeResult = new AlipaySystemOauthTokenResponse($largeResponse);
        $this->assertSame(999999999, $largeResult->getExpiresIn());
        $this->assertSame(999999999, $largeResult->getReExpiresIn());
    }

    public function testConstructorWithMissingOptionalFields(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123',
            // 只有必要字段
        ];

        $response = new AlipaySystemOauthTokenResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertNull($response->getMsg());
        $this->assertNull($response->getSubCode());
        $this->assertNull($response->getSubMsg());
        $this->assertNull($response->getOpenId());
        $this->assertNull($response->getAccessToken());
        $this->assertNull($response->getRefreshToken());
        $this->assertNull($response->getExpiresIn());
        $this->assertNull($response->getReExpiresIn());
        $this->assertNull($response->getAuthStart());
    }
}
