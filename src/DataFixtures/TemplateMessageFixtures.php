<?php

namespace AlipayMiniProgramBundle\DataFixtures;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class TemplateMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $templateMessage = new TemplateMessage();
        $templateMessage->setMiniProgram($this->getReference(MiniProgramFixtures::MINI_PROGRAM_REFERENCE, MiniProgram::class));
        $templateMessage->setToUser($this->getReference(UserFixtures::USER_REFERENCE, User::class));
        $templateMessage->setTemplateId('test_template_id_123456');
        $templateMessage->setToOpenId('test_open_id_123456');
        $templateMessage->setPage('pages/index/index');
        $templateMessage->setData([
            'keyword1' => ['value' => '测试标题'],
            'keyword2' => ['value' => '测试内容'],
            'keyword3' => ['value' => '2024-01-01 12:00:00'],
        ]);
        $templateMessage->setIsSent(false);

        $manager->persist($templateMessage);
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
