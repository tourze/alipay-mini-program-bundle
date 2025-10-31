<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = 'user-test';

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setMiniProgram($this->getReference(MiniProgramFixtures::MINI_PROGRAM_REFERENCE, MiniProgram::class));
        $user->setOpenId('test_open_id_123456');
        $user->setNickName('测试用户');
        $user->setAvatar('https://avatars.alipay.com/default_avatar.jpg');
        $user->setProvince('广东省');
        $user->setCity('深圳市');
        $user->setGender(AlipayUserGender::MALE);
        $user->setLastInfoUpdateTime(new \DateTimeImmutable());

        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MiniProgramFixtures::class,
        ];
    }
}
