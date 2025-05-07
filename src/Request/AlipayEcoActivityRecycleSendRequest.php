<?php

namespace AlipayMiniProgramBundle\Request;

require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AlipayConfig.php';
require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AopClient.php';
require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/request/AlipayEcoActivityRecycleSendRequest.php';

use AlipayMiniProgramBundle\Response\AlipaySystemOauthTokenResponse;
use Psr\Log\LoggerInterface;

/**
 * 发放蚂蚁森林能量
 */
class AlipayEcoActivityRecycleSendRequest
{
    private string $apiMethodName = 'alipay.eco.activity.recycle.send';

    private string $sellerId;

    private string $outBizNo;

    private int $quantity = 0;

    public function __construct(
        private readonly \AlipayConfig $config,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function setSellerId(string $sellerId): void
    {
        $this->sellerId = $sellerId;
    }

    public function send(): AlipaySystemOauthTokenResponse
    {
        $client = new \AopClient($this->config);
        $request = new \AlipayEcoActivityRecycleSendRequest();
        $request->setBizContent(json_encode([
            'buyer_id' => $this->config->getAppId(),
            'seller_id' => $this->sellerId,
            'out_biz_no' => $this->outBizNo,
            'out_biz_type' => 'RECYCLING',
            'item_list' => [
                [
                    'item_name' => '胶囊咖啡',
                    'quantity' => $this->quantity,
                    'items' => [
                        [
                            'ext_key' => 'ITEM_TYPE',
                            'ext_value' => 'cans',
                        ],
                    ],
                ],
            ],
        ]));

        $result = $client->execute($request);
        $responseName = str_replace('.', '_', $this->apiMethodName) . '_response';
        $response = $result->$responseName;
        $this->logger->info('AlipayEcoActivityRecycleSendRequest', [
            'request' => $request,
            'result' => $result->$responseName,
        ]);

        return new AlipaySystemOauthTokenResponse($response);
    }

    public function setOutBizNo(string $outBizNo): void
    {
        $this->outBizNo = $outBizNo;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
