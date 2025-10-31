<?php

namespace AlipayMiniProgramBundle\Response;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Utils\ResponseSanitizer;

class AlipayUserInfoShareResponse
{
    private bool $success;

    private ?string $code;

    private ?string $msg;

    private ?string $subCode;

    private ?string $subMsg;

    private ?string $userId;

    private ?string $avatar;

    private ?string $province;

    private ?string $city;

    private ?string $nickName;

    private ?AlipayUserGender $gender;

    public function __construct(\stdClass $response)
    {
        $code = ResponseSanitizer::expectNullableString($response, 'code');
        $this->success = null === $code || '10000' === $code;
        $this->code = $code;
        $this->msg = ResponseSanitizer::expectNullableString($response, 'msg');
        $this->subCode = ResponseSanitizer::expectNullableString($response, 'sub_code');
        $this->subMsg = ResponseSanitizer::expectNullableString($response, 'sub_msg');
        $this->userId = ResponseSanitizer::expectNullableString($response, 'user_id');
        $this->avatar = ResponseSanitizer::expectNullableString($response, 'avatar');
        $this->province = ResponseSanitizer::expectNullableString($response, 'province');
        $this->city = ResponseSanitizer::expectNullableString($response, 'city');
        $this->nickName = ResponseSanitizer::expectNullableString($response, 'nick_name');
        $genderValue = ResponseSanitizer::expectNullableString($response, 'gender');
        $this->gender = null !== $genderValue ? AlipayUserGender::from($genderValue) : null;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function getSubCode(): ?string
    {
        return $this->subCode;
    }

    public function getSubMsg(): ?string
    {
        return $this->subMsg;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function getGender(): ?AlipayUserGender
    {
        return $this->gender;
    }
}
