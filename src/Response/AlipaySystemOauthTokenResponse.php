<?php

namespace AlipayMiniProgramBundle\Response;

class AlipaySystemOauthTokenResponse
{
    private bool $success;

    private ?string $code;

    private ?string $msg;

    private ?string $subCode;

    private ?string $subMsg;

    private ?string $userId;

    private ?string $openId;

    private ?string $accessToken;

    private ?string $refreshToken;

    private ?int $expiresIn;

    private ?int $reExpiresIn;

    private ?string $authStart;

    public function __construct(\stdClass $response)
    {
        $this->success = empty($response->code) || '10000' === $response->code;
        $this->code = $response->code ?? null;
        $this->msg = $response->msg ?? null;
        $this->subCode = $response->sub_code ?? null;
        $this->subMsg = $response->sub_msg ?? null;
        $this->userId = $response->user_id ?? null;
        $this->openId = $response->open_id ?? null;
        $this->accessToken = $response->access_token ?? null;
        $this->refreshToken = $response->refresh_token ?? null;
        $this->expiresIn = $response->expires_in ? (int) $response->expires_in : null;
        $this->reExpiresIn = $response->re_expires_in ? (int) $response->re_expires_in : null;
        $this->authStart = $response->auth_start ?? null;
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

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function getReExpiresIn(): ?int
    {
        return $this->reExpiresIn;
    }

    public function getAuthStart(): ?string
    {
        return $this->authStart;
    }
}
