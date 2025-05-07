<?php

namespace AlipayMiniProgramBundle\Request;

require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AlipayConfig.php';
require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AopClient.php';
require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/request/AlipaySystemOauthTokenRequest.php';

use AlipayMiniProgramBundle\Response\AlipaySystemOauthTokenResponse;
use Psr\Log\LoggerInterface;

class AlipaySystemOauthTokenRequest
{
    private string $apiMethodName = 'alipay.system.oauth.token';

    private string $code;

    private string $grantType;

    private ?string $refreshToken = null;

    public function __construct(
        private readonly \AlipayConfig $config,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setGrantType(string $grantType): void
    {
        $this->grantType = $grantType;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getToken(): AlipaySystemOauthTokenResponse
    {
        $client = new \AopClient($this->config);
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setCode($this->code);
        $request->setGrantType($this->grantType);
        if ($this->refreshToken) {
            $request->setRefreshToken($this->refreshToken);
        }

        $result = $client->execute($request);
        $responseName = str_replace('.', '_', $this->apiMethodName) . '_response';
        $response = $result->$responseName;
        $this->logger->info('AlipaySystemOauthTokenRequest', [
            'request' => $request,
            'result' => $result->$responseName,
        ]);

        return new AlipaySystemOauthTokenResponse($response);
    }
}
