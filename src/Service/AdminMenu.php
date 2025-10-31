<?php

declare(strict_types=1);

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\AuthCode;
use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\TemplateMessage;
use AlipayMiniProgramBundle\Entity\User;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('支付宝小程序')) {
            $item->addChild('支付宝小程序')->setExtra('permission', 'AlipayMiniProgramBundle');
        }

        $alipayMenu = $item->getChild('支付宝小程序');
        if (null !== $alipayMenu) {
            $alipayMenu->addChild('小程序配置')->setUri($this->linkGenerator->getCurdListPage(MiniProgram::class));
            $alipayMenu->addChild('支付宝用户')->setUri($this->linkGenerator->getCurdListPage(User::class));
            $alipayMenu->addChild('授权码记录')->setUri($this->linkGenerator->getCurdListPage(AuthCode::class));
            $alipayMenu->addChild('表单ID管理')->setUri($this->linkGenerator->getCurdListPage(FormId::class));
            $alipayMenu->addChild('模板消息')->setUri($this->linkGenerator->getCurdListPage(TemplateMessage::class));
            $alipayMenu->addChild('手机号码')->setUri($this->linkGenerator->getCurdListPage(Phone::class));
            $alipayMenu->addChild('用户手机号绑定')->setUri($this->linkGenerator->getCurdListPage(AlipayUserPhone::class));
        }
    }
}
