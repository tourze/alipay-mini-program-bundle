<?php

namespace AlipayMiniProgramBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AlipayAuthScope: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 静默授权，获取用户 userId 和 openId
     */
    case AUTH_BASE = 'auth_base';

    /**
     * 用户信息授权，获取用户昵称、头像等信息
     */
    case AUTH_USER = 'auth_user';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUTH_BASE => '基础授权',
            self::AUTH_USER => '用户信息授权',
        };
    }
}
