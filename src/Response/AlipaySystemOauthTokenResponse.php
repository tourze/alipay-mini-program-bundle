<?php

namespace AlipayMiniProgramBundle\Response;

use AlipayMiniProgramBundle\Utils\ResponseSanitizer;

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
        $code = ResponseSanitizer::expectNullableString($response, 'code');
        $this->success = null === $code || '10000' === $code;
        $this->code = $code;
        $this->msg = ResponseSanitizer::expectNullableString($response, 'msg');
        $this->subCode = ResponseSanitizer::expectNullableString($response, 'sub_code');
        $this->subMsg = ResponseSanitizer::expectNullableString($response, 'sub_msg');
        $this->userId = ResponseSanitizer::expectNullableString($response, 'user_id');
        $this->openId = ResponseSanitizer::expectNullableString($response, 'open_id');
        $this->accessToken = ResponseSanitizer::expectNullableString($response, 'access_token');
        $this->refreshToken = ResponseSanitizer::expectNullableString($response, 'refresh_token');
        $expiresInValue = ResponseSanitizer::expectNullableInt($response, 'expires_in');
        // 0表示立即过期，等同于无有效期，返回null；负值保留（可能有特殊业务含义）
        $this->expiresIn = (null !== $expiresInValue && 0 !== $expiresInValue) ? $expiresInValue : null;
        $reExpiresInValue = ResponseSanitizer::expectNullableInt($response, 're_expires_in');
        $this->reExpiresIn = (null !== $reExpiresInValue && 0 !== $reExpiresInValue) ? $reExpiresInValue : null;
        $this->authStart = ResponseSanitizer::expectNullableString($response, 'auth_start');
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
