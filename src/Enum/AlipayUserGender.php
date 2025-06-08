<?php

namespace AlipayMiniProgramBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AlipayUserGender: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 女性
     */
    case FEMALE = 'F';

    /**
     * 男性
     */
    case MALE = 'M';

    public function getLabel(): string
    {
        return match ($this) {
            self::FEMALE => '女',
            self::MALE => '男',
        };
    }
}
