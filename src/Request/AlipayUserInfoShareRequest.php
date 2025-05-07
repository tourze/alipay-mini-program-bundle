<?php

namespace AlipayMiniProgramBundle\Request;

require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AlipayConfig.php';

use AlipayMiniProgramBundle\Response\AlipayUserInfoShareResponse;

class AlipayUserInfoShareRequest
{
    private string $apiMethodName = 'alipay.user.info.share';

    public function __construct(private readonly \AlipayConfig $config)
    {
    }

    public function getInfo(string $accessToken): AlipayUserInfoShareResponse
    {
        $client = new \AopClient($this->config);
        $request = new \AlipayUserInfoShareRequest();

        $result = $client->execute($request, $accessToken);
        $responseName = str_replace('.', '_', $this->apiMethodName) . '_response';
        $response = $result->$responseName;

        return new AlipayUserInfoShareResponse($response);
    }
}
