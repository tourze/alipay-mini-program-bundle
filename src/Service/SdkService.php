<?php

namespace AlipayMiniProgramBundle\Service;

require_once __DIR__ . '/../../../../vendor/alipaysdk/openapi/v2/aop/AlipayConfig.php';

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;

class SdkService
{
    private array $sdkInstances = [];

    /**
     * 通过小程序获取 SDK 实例
     */
    public function getSdkFromMiniProgram(MiniProgram $miniProgram): \AlipayConfig
    {
        $key = sprintf('miniprogram_%s', $miniProgram->getId());
        if (!isset($this->sdkInstances[$key])) {
            $config = new \AlipayConfig();
            $config->setAppId($miniProgram->getAppId());
            $config->setPrivateKey($miniProgram->getPrivateKey());
            $config->setAlipayPublicKey($miniProgram->getAlipayPublicKey());
            if ($miniProgram->getEncryptKey()) {
                $config->setEncryptKey($miniProgram->getEncryptKey());
            }
            $config->setSignType($miniProgram->getSignType());
            $config->setServerUrl($miniProgram->getGatewayUrl());

            $this->sdkInstances[$key] = $config;
        }

        return $this->sdkInstances[$key];
    }

    /**
     * 通过用户获取 SDK 实例
     */
    public function getSdkFromUser(User $user): \AlipayConfig
    {
        return $this->getSdkFromMiniProgram($user->getMiniProgram());
    }
}
