<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_user', options: ['comment' => '支付宝小程序用户表'])]
#[AsEntityListener]
class User implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: MiniProgram::class)]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的支付宝小程序'])]
    private MiniProgram $miniProgram;

    #[ORM\Column(length: 64, unique: true, options: ['comment' => '支付宝用户的 open_id'])]
    private string $openId;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '用户昵称'])]
    private ?string $nickName = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '用户头像地址'])]
    private ?string $avatar = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '省份名称'])]
    private ?string $province = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '城市名称'])]
    private ?string $city = null;

    #[ORM\Column(length: 8, enumType: AlipayUserGender::class, nullable: true, options: ['comment' => '用户性别'])]
    private ?AlipayUserGender $gender = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后一次获取用户信息的时间'])]
    private ?\DateTimeImmutable $lastInfoUpdateTime = null;

    /**
     * @var Collection<int, AuthCode>
     */
    #[ORM\OneToMany(mappedBy: 'alipayUser', targetEntity: AuthCode::class)]
    private Collection $authCodes;

    /**
     * @var Collection<int, AlipayUserPhone>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AlipayUserPhone::class)]
    private Collection $userPhones;

    use TimestampableAware;

    public function __construct()
    {
        $this->authCodes = new ArrayCollection();
        $this->userPhones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMiniProgram(): MiniProgram
    {
        return $this->miniProgram;
    }

    public function setMiniProgram(MiniProgram $miniProgram): static
    {
        $this->miniProgram = $miniProgram;

        return $this;
    }

    public function getOpenId(): string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): static
    {
        $this->openId = $openId;

        return $this;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): static
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getGender(): ?AlipayUserGender
    {
        return $this->gender;
    }

    public function setGender(?AlipayUserGender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLastInfoUpdateTime(): ?\DateTimeImmutable
    {
        return $this->lastInfoUpdateTime;
    }

    public function setLastInfoUpdateTime(?\DateTimeInterface $lastInfoUpdateTime): static
    {
        $this->lastInfoUpdateTime = $lastInfoUpdateTime instanceof \DateTimeImmutable 
            ? $lastInfoUpdateTime 
            : ($lastInfoUpdateTime !== null ? \DateTimeImmutable::createFromInterface($lastInfoUpdateTime) : null);

        return $this;
    }

    /**
     * @return Collection<int, AuthCode>
     */
    public function getAuthCodes(): Collection
    {
        return $this->authCodes;
    }

    public function addAuthCode(AuthCode $authCode): static
    {
        if (!$this->authCodes->contains($authCode)) {
            $this->authCodes->add($authCode);
            $authCode->setAlipayUser($this);
        }

        return $this;
    }

    public function removeAuthCode(AuthCode $authCode): static
    {
        if ($this->authCodes->removeElement($authCode)) {
            if ($authCode->getAlipayUser() === $this) {
                $authCode->setAlipayUser(null);
            }
        }

        return $this;
    }

    public function getLatestAuthCode(): ?AuthCode
    {
        return $this->authCodes->last() ?: null;
    }

    /**
     * @return Collection<int, AlipayUserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(AlipayUserPhone $userPhone): static
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones->add($userPhone);
            $userPhone->setUser($this);
        }

        return $this;
    }

    public function removeUserPhone(AlipayUserPhone $userPhone): static
    {
        if ($this->userPhones->removeElement($userPhone)) {
            if ($userPhone->getUser() === $this) {
                $userPhone->setUser(null);
            }
        }

        return $this;
    }

    public function getLatestPhone(): ?Phone
    {
        $latestUserPhone = $this->userPhones->last();

        return $latestUserPhone ? $latestUserPhone->getPhone() : null;
    }

    public function __toString(): string
    {
        return $this->nickName ?? $this->openId ?? 'User #' . ($this->id ?? 0);
    }
}
