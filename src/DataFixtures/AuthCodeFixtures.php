<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AuthCodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $authCode = new AuthCode();
        $authCode->setAlipayUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $authCode->setAuthCode('test_auth_code_123456');
        $authCode->setScope(AlipayAuthScope::AUTH_USER);
        $authCode->setState('test_state');
        $authCode->setUserId('test_user_id_123456');
        $authCode->setOpenId('test_open_id_123456');
        $authCode->setAccessToken('test_access_token_123456');
        $authCode->setRefreshToken('test_refresh_token_123456');
        $authCode->setExpiresIn(7200);
        $authCode->setReExpiresIn(2592000);
        $authCode->setAuthStart(new \DateTimeImmutable());
        $authCode->setSign('test_sign');

        $manager->persist($authCode);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
