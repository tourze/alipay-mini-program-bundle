<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class PhoneFixtures extends Fixture
{
    public const PHONE_REFERENCE = 'phone-test';

    public function load(ObjectManager $manager): void
    {
        $phone = new Phone();
        $phone->setNumber('13800138000');

        $manager->persist($phone);
        $this->addReference(self::PHONE_REFERENCE, $phone);

        $manager->flush();
    }
}
