<?php

namespace AlipayMiniProgramBundle;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;

#[MenuProvider]
class AdminMenu
{
    public function __invoke(ItemInterface $item): void
    {
    }
}
