<?php

namespace AlipayMiniProgramBundle\Enum;

enum AlipayAuthScope: string
{
    /**
     * 静默授权，获取用户 userId 和 openId
     */
    case AUTH_BASE = 'auth_base';

    /**
     * 用户信息授权，获取用户昵称、头像等信息
     */
    case AUTH_USER = 'auth_user';
}
