<?php

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Repository\UserRepository;
use App\Service\JwtUserProviderInterface;
use AppBundle\Entity\BizUser;
use Lcobucci\JWT\UnencryptedToken;

/**
 * 支付宝小程序 JWT 用户提供者
 */
class JwtUserProvider implements JwtUserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
    ) {
    }

    /**
     * 从 JWT 令牌中获取业务用户
     */
    public function getUserFromToken(UnencryptedToken $token): ?BizUser
    {
        $claims = $token->claims();
        $uid = $claims->get('uid');
        $openId = $claims->get('open_id');

        if (!$uid || !$openId) {
            return null;
        }

        $alipayUser = $this->userRepository->findOneBy([
            'id' => $uid,
            'openId' => $openId,
        ]);

        if (!$alipayUser) {
            return null;
        }

        // 转换为 BizUser
        return $this->userService->getBizUser($alipayUser);
    }

    /**
     * 获取此提供者支持的用户类型
     */
    public function supports(string $type): bool
    {
        return 'alipay' === $type;
    }

    /**
     * 为用户生成 JWT 声明
     *
     * @param object $user 子模块用户实体
     */
    public function createClaimsForUser(object $user): array
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be an instance of AlipayMiniProgramBundle\Entity\User');
        }

        return [
            'uid' => $user->getId(),
            'type' => 'alipay',
            'open_id' => $user->getOpenId(),
        ];
    }
}
