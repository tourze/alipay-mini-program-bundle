<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class MiniProgramFixtures extends Fixture
{
    public const MINI_PROGRAM_REFERENCE = 'mini-program-test';

    public function load(ObjectManager $manager): void
    {
        $miniProgram = new MiniProgram();
        $miniProgram->setName('测试支付宝小程序');
        $miniProgram->setAppId('2021000000000001');
        $miniProgram->setPrivateKey('TEST_PRIVATE_KEY');
        $miniProgram->setAlipayPublicKey('TEST_ALIPAY_PUBLIC_KEY');
        $miniProgram->setSandbox(true);
        $miniProgram->setSignType('RSA2');
        $miniProgram->setGatewayUrl('https://openapi.alipaydev.com/gateway.do');
        $miniProgram->setRemark('测试环境的支付宝小程序配置');

        $manager->persist($miniProgram);
        $this->addReference(self::MINI_PROGRAM_REFERENCE, $miniProgram);

        $manager->flush();
    }
}
