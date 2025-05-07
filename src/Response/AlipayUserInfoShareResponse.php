<?php

namespace AlipayMiniProgramBundle\Response;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;

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
        $this->success = empty($response->code) || '10000' === $response->code;
        $this->code = $response->code ?? null;
        $this->msg = $response->msg ?? null;
        $this->subCode = $response->sub_code ?? null;
        $this->subMsg = $response->sub_msg ?? null;
        $this->userId = $response->user_id ?? null;
        $this->avatar = $response->avatar ?? null;
        $this->province = $response->province ?? null;
        $this->city = $response->city ?? null;
        $this->nickName = $response->nick_name ?? null;
        $this->gender = isset($response->gender) ? AlipayUserGender::from($response->gender) : null;
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
