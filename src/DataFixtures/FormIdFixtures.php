<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class FormIdFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $formId = new FormId();
        $formId->setMiniProgram($this->getReference(MiniProgramFixtures::MINI_PROGRAM_REFERENCE, MiniProgram::class));
        $formId->setUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $formId->setFormId('test_form_id_123456789');
        $formId->setExpireTime(new \DateTimeImmutable('+7 days'));
        $formId->setUsedCount(0);

        $manager->persist($formId);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MiniProgramFixtures::class,
            UserFixtures::class,
        ];
    }
}
