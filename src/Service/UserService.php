<?php

namespace AlipayMiniProgramBundle\Service;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use AlipayMiniProgramBundle\Entity\Phone;
use AlipayMiniProgramBundle\Entity\User;
use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Repository\PhoneRepository;
use AlipayMiniProgramBundle\Repository\UserRepository;
use BizUserBundle\Entity\BizUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\JsonRPC\Core\Exception\ApiException;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PhoneRepository $phoneRepository,
        private readonly UserLoaderInterface $userLoader,
        private readonly UserRepository $alipayUserRepository,
    ) {
    }

    /**
     * 更新用户信息
     *
     * @param array{
     *     nick_name?: string,
     *     avatar?: string,
     *     province?: string,
     *     city?: string,
     *     gender?: string
     * } $userInfo
     */
    public function updateUserInfo(User $user, array $userInfo): void
    {
        if (isset($userInfo['nick_name'])) {
            $user->setNickName($userInfo['nick_name']);
        }
        if (isset($userInfo['avatar'])) {
            $user->setAvatar($userInfo['avatar']);
        }
        if (isset($userInfo['province'])) {
            $user->setProvince($userInfo['province']);
        }
        if (isset($userInfo['city'])) {
            $user->setCity($userInfo['city']);
        }
        if (isset($userInfo['gender'])) {
            $user->setGender(AlipayUserGender::from($userInfo['gender']));
        }
        $user->setLastInfoUpdateTime(new \DateTime());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * 绑定手机号码
     */
    public function bindPhone(User $user, string $phoneNumber): void
    {
        // 查找或创建手机号码记录
        $phone = $this->phoneRepository->findByNumber($phoneNumber);
        if (!$phone) {
            $phone = new Phone();
            $phone->setNumber($phoneNumber);
            $this->entityManager->persist($phone);
        }

        // 创建关联记录
        $userPhone = new AlipayUserPhone();
        $userPhone->setUser($user);
        $userPhone->setPhone($phone);
        $userPhone->setVerifiedTime(new \DateTime());

        $this->entityManager->persist($userPhone);
        $this->entityManager->flush();
    }

    /**
     * 获取用户最新绑定的手机号码
     */
    public function getLatestPhone(User $user): ?string
    {
        $phone = $user->getLatestPhone();

        return $phone?->getNumber();
    }

    /**
     * 获取用户所有绑定的手机号码
     *
     * @return string[]
     */
    public function getAllPhones(User $user): array
    {
        return $user->getUserPhones()
            ->map(fn (AlipayUserPhone $userPhone) => $userPhone->getPhone()->getNumber())
            ->toArray();
    }

    /**
     * 获取或创建业务用户
     */
    public function getBizUser(User $user): UserInterface
    {
        // 先通过手机号查找业务用户 todo 与已有帐号合并问题
        $phone = $this->getLatestPhone($user);
        //        if ($phone) {
        //            $bizUser = $this->bizUserRepository->findOneBy(['mobile' => $phone]);
        //            if ($bizUser) {
        //                // 更新用户信息
        //                $bizUser->setNickName($user->getNickName() ?? '支付宝用户');
        //                $bizUser->setAvatar($user->getAvatar());
        //                $this->entityManager->persist($bizUser);
        //                $this->entityManager->flush();
        //                return $bizUser;
        //            }
        //        }

        $bizUser = $this->userLoader->loadUserByIdentifier($user->getOpenId());
        if ($bizUser) {
            return $bizUser;
        }

        // 如果没有找到，创建新用户
        $bizUser = new BizUser();
        $bizUser->setUsername($user->getOpenId());
        $bizUser->setIdentity($user->getOpenId());
        if ($phone) {
            $bizUser->setMobile($phone);
        }
        $bizUser->setNickName($user->getNickName() ?? '支付宝用户');
        $bizUser->setAvatar($user->getAvatar());
        $bizUser->setValid(true);

        $this->entityManager->persist($bizUser);
        $this->entityManager->flush();

        return $bizUser;
    }

    /**
     * 通过业务用户查找支付宝用户
     */
    public function getAlipayUser(BizUser|UserInterface $bizUser): ?User
    {
        // 1. 先通过 username 查找（username 是 openId）
        $alipayUser = $this->alipayUserRepository->findOneBy(['openId' => $bizUser->getUsername()]);
        if ($alipayUser) {
            return $alipayUser;
        }

        // 2. 如果没找到，通过手机号查找 todo 与已有帐号合并问题
        //        if ($bizUser->getMobile()) {
        //            $alipayUser = $this->alipayUserRepository->findOneBy([
        //                'mobile' => $bizUser->getMobile(),
        //            ]);
        //            if ($alipayUser) {
        //                return $alipayUser;
        //            }
        //        }

        return null;
    }

    /**
     * 通过业务用户查找支付宝用户，如果找不到则抛出异常
     *
     * @throws ApiException 如果找不到支付宝用户
     */
    public function requireAlipayUser(BizUser|UserInterface $bizUser): User
    {
        $alipayUser = $this->getAlipayUser($bizUser);
        if (!$alipayUser) {
            throw new ApiException('未找到对应的支付宝用户');
        }

        return $alipayUser;
    }
}
