<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AlipayUserPhoneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $userPhone->setPhone($this->getReference(PhoneFixtures::PHONE_REFERENCE, Phone::class));
        $userPhone->setVerifiedTime(new \DateTimeImmutable());

        $manager->persist($userPhone);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PhoneFixtures::class,
        ];
    }
}
