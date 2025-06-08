<?php

namespace AlipayMiniProgramBundle\Tests\Response;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Response\AlipayUserInfoShareResponse;
use PHPUnit\Framework\TestCase;

class AlipayUserInfoShareResponseTest extends TestCase
{
    public function test_constructor_with_successful_response(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'msg' => 'Success',
            'user_id' => 'test_user_123',
            'avatar' => 'https://example.com/avatar.jpg',
            'province' => '浙江省',
            'city' => '杭州市',
            'nick_name' => '测试用户',
            'gender' => 'M'
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

    public function test_constructor_with_failed_response(): void
    {
        $responseData = (object) [
            'code' => '40001',
            'msg' => 'Missing Required Arguments',
            'sub_code' => 'isv.missing-signature',
            'sub_msg' => '缺少签名参数'
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

    public function test_constructor_with_empty_response(): void
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

    public function test_constructor_with_female_gender(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'gender' => 'F'
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertSame(AlipayUserGender::FEMALE, $response->getGender());
    }

    public function test_constructor_with_invalid_gender_throws_exception(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'gender' => 'U'
        ];

        $this->expectException(\ValueError::class);
        new AlipayUserInfoShareResponse($responseData);
    }

    public function test_constructor_without_gender(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123'
        ];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertNull($response->getGender());
    }

    public function test_is_success_with_null_code(): void
    {
        $responseData = new \stdClass();

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function test_is_success_with_success_code(): void
    {
        $responseData = (object) ['code' => '10000'];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertTrue($response->isSuccess());
    }

    public function test_is_success_with_error_code(): void
    {
        $responseData = (object) ['code' => '40001'];

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertFalse($response->isSuccess());
    }

    public function test_constructor_with_partial_data(): void
    {
        $responseData = (object) [
            'code' => '10000',
            'user_id' => 'test_user_123',
            'nick_name' => '测试用户'
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

    public function test_all_getter_methods_exist(): void
    {
        $methods = [
            'isSuccess', 'getCode', 'getMsg', 'getSubCode', 'getSubMsg',
            'getUserId', 'getAvatar', 'getProvince', 'getCity', 'getNickName', 'getGender'
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(AlipayUserInfoShareResponse::class, $method),
                "Method {$method} should exist"
            );
        }
    }

    public function test_constructor_with_all_fields(): void
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
            'gender' => 'M'
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

    public function test_constructor_accepts_stdclass(): void
    {
        $responseData = new \stdClass();
        $responseData->code = '10000';

        $response = new AlipayUserInfoShareResponse($responseData);

        $this->assertInstanceOf(AlipayUserInfoShareResponse::class, $response);
        $this->assertTrue($response->isSuccess());
    }

    public function test_response_immutability(): void
    {
        $responseData = (object) ['code' => '10000'];
        $response = new AlipayUserInfoShareResponse($responseData);

        // 验证没有setter方法
        $reflection = new \ReflectionClass($response);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $setterMethods = array_filter($methods, function($method) {
            return str_starts_with($method->getName(), 'set');
        });

        $this->assertEmpty($setterMethods, 'Response should be immutable - no setter methods allowed');
    }

    public function test_constructor_parameter_type(): void
    {
        $reflection = new \ReflectionClass(AlipayUserInfoShareResponse::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);

        $parameter = $parameters[0];
        $this->assertSame('response', $parameter->getName());
        $this->assertSame('stdClass', $parameter->getType()->getName());
    }

    public function test_gender_enum_integration(): void
    {
        $maleResponse = (object) ['gender' => 'M'];
        $femaleResponse = (object) ['gender' => 'F'];

        $maleResult = new AlipayUserInfoShareResponse($maleResponse);
        $femaleResult = new AlipayUserInfoShareResponse($femaleResponse);

        $this->assertSame(AlipayUserGender::MALE, $maleResult->getGender());
        $this->assertSame(AlipayUserGender::FEMALE, $femaleResult->getGender());
    }
} 