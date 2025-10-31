<?php

namespace AlipayMiniProgramBundle\Tests\Response;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Response\AlipayUserInfoShareResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AlipayUserInfoShareResponse::class)]
final class AlipayUserInfoShareResponseTest extends TestCase
{
    public function testConstructorWithSuccessfulResponse(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'msg' => 'Success',
            'user_id' => 'test_user_123',
            'avatar' => 'https://example.com/avatar.jpg',
            'province' => '浙江省',
            'city' => '杭州市',
            'nick_name' => '测试用户',
            'gender' => 'M',
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('10000', $response->getCode());
        $this->assertSame('Success', $response->getMsg());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('https://example.com/avatar.jpg', $response->getAvatar());
        $this->assertSame('浙江省', $response->getProvince());
        $this->assertSame('杭州市', $response->getCity());
        $this->assertSame('测试用户', $response->getNickName());
        $this->assertSame(AlipayUserGender::MALE, $response->getGender());
    }

    public function testConstructorWithFailedResponse(): void
    {
        $responseData = (object) [
            'code' => '40001',
            'msg' => 'Missing Required Arguments',
            'sub_code' => 'isv.missing-signature',
            'sub_msg' => '缺少签名参数',
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertFalse($response->isSuccess());
        $this->assertSame('40001', $response->getCode());
        $this->assertSame('Missing Required Arguments', $response->getMsg());
        $this->assertSame('isv.missing-signature', $response->getSubCode());
        $this->assertSame('缺少签名参数', $response->getSubMsg());
        $this->assertNull($response->getUserId());
        $this->assertNull($response->getAvatar());
        $this->assertNull($response->getProvince());
        $this->assertNull($response->getCity());
        $this->assertNull($response->getNickName());
        $this->assertNull($response->getGender());
    }

    public function testConstructorWithEmptyResponse(): void
    {
        $responseData = new \stdClass();

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess()); // 空code被认为是成功
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMsg());
        $this->assertNull($response->getSubCode());
        $this->assertNull($response->getSubMsg());
        $this->assertNull($response->getUserId());
        $this->assertNull($response->getAvatar());
        $this->assertNull($response->getProvince());
        $this->assertNull($response->getCity());
        $this->assertNull($response->getNickName());
        $this->assertNull($response->getGender());
    }

    public function testConstructorWithFemaleGender(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'gender' => 'F',
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertSame(AlipayUserGender::FEMALE, $response->getGender());
    }

    public function testConstructorWithInvalidGenderThrowsException(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'gender' => 'U',
        ];

        $this->expectException(\ValueError::class);
        new AlipayUserInfoShareResponse($responseData);
    }

    public function testConstructorWithoutGender(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123',
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertNull($response->getGender());
    }

    public function testIsSuccessWithNullCode(): void
    {
        $responseData = new \stdClass();

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessWithSuccessCode(): void
    {
        $responseData = (object) ['code' => '10000'];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testIsSuccessWithErrorCode(): void
    {
        $responseData = (object) ['code' => '40001'];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertFalse($response->isSuccess());
    }

    public function testConstructorWithPartialData(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123',
            'nick_name' => '测试用户',
            // 缺少其他字段
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('测试用户', $response->getNickName());
        $this->assertNull($response->getAvatar());
        $this->assertNull($response->getProvince());
        $this->assertNull($response->getCity());
        $this->assertNull($response->getGender());
    }

    public function testAllGetterMethodsExist(): void
    {
        $methods = [
            'isSuccess', 'getCode', 'getMsg', 'getSubCode', 'getSubMsg',
            'getUserId', 'getAvatar', 'getProvince', 'getCity', 'getNickName', 'getGender',
        ];

        foreach ($methods as $method) {
            // Verify all required methods exist by creating instance and checking method
            $instance = new AlipayUserInfoShareResponse((object) ['test' => 'response']);
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
            'avatar' => 'https://example.com/avatar.jpg',
            'province' => '浙江省',
            'city' => '杭州市',
            'nick_name' => '测试用户',
            'gender' => 'M',
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('10000', $response->getCode());
        $this->assertSame('Success', $response->getMsg());
        $this->assertSame('test_sub_code', $response->getSubCode());
        $this->assertSame('test_sub_msg', $response->getSubMsg());
        $this->assertSame('test_user_123', $response->getUserId());
        $this->assertSame('https://example.com/avatar.jpg', $response->getAvatar());
        $this->assertSame('浙江省', $response->getProvince());
        $this->assertSame('杭州市', $response->getCity());
        $this->assertSame('测试用户', $response->getNickName());
        $this->assertSame(AlipayUserGender::MALE, $response->getGender());
    }

    public function testConstructorAcceptsStdclass(): void
    {
        $responseData = new \stdClass();
        $responseData->code = '10000';

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function testResponseImmutability(): void
    {
        $responseData = (object) ['code' => '10000'];
        $response = new AlipayUserInfoShareResponse($responseData);

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
        $reflection = new \ReflectionClass(AlipayUserInfoShareResponse::class);
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

    public function testGenderEnumIntegration(): void
    {
        $maleResponse = (object) ['gender' => 'M'];
        $femaleResponse = (object) ['gender' => 'F'];

        $maleResult = new AlipayUserInfoShareResponse($maleResponse);
        $femaleResult = new AlipayUserInfoShareResponse($femaleResponse);

        $this->assertSame(AlipayUserGender::MALE, $maleResult->getGender());
        $this->assertSame(AlipayUserGender::FEMALE, $femaleResult->getGender());
    }
}
