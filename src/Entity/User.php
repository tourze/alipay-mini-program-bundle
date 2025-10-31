<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Enum\AlipayUserGender;
use AlipayMiniProgramBundle\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_user', options: ['comment' => '支付宝小程序用户表'])]
#[AsEntityListener]
class User implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: MiniProgram::class)]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的支付宝小程序'])]
    private MiniProgram $miniProgram;

    #[ORM\Column(length: 64, unique: true, options: ['comment' => '支付宝用户的 open_id'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $openId;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => '用户昵称'])]
    #[Assert\Length(max: 64)]
    private ?string $nickName = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '用户头像地址'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url(message: '请输入有效的URL地址')]
    private ?string $avatar = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '省份名称'])]
    #[Assert\Length(max: 32)]
    private ?string $province = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '城市名称'])]
    #[Assert\Length(max: 32)]
    private ?string $city = null;

    #[ORM\Column(length: 8, enumType: AlipayUserGender::class, nullable: true, options: ['comment' => '用户性别'])]
    #[Assert\Choice(callback: [AlipayUserGender::class, 'cases'])]
    private ?AlipayUserGender $gender = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后一次获取用户信息的时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
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

    public function setMiniProgram(MiniProgram $miniProgram): void
    {
        $this->miniProgram = $miniProgram;
    }

    public function getOpenId(): string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): void
    {
        $this->openId = $openId;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): void
    {
        $this->nickName = $nickName;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): void
    {
        $this->province = $province;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getGender(): ?AlipayUserGender
    {
        return $this->gender;
    }

    public function setGender(?AlipayUserGender $gender): void
    {
        $this->gender = $gender;
    }

    public function getLastInfoUpdateTime(): ?\DateTimeImmutable
    {
        return $this->lastInfoUpdateTime;
    }

    public function setLastInfoUpdateTime(?\DateTimeInterface $lastInfoUpdateTime): void
    {
        $this->lastInfoUpdateTime = $lastInfoUpdateTime instanceof \DateTimeImmutable
            ? $lastInfoUpdateTime
            : (null !== $lastInfoUpdateTime ? \DateTimeImmutable::createFromInterface($lastInfoUpdateTime) : null);
    }

    /**
     * @return Collection<int, AuthCode>
     */
    public function getAuthCodes(): Collection
    {
        return $this->authCodes;
    }

    public function addAuthCode(AuthCode $authCode): void
    {
        if (!$this->authCodes->contains($authCode)) {
            $this->authCodes->add($authCode);
            $authCode->setAlipayUser($this);
        }
    }

    public function removeAuthCode(AuthCode $authCode): void
    {
        if ($this->authCodes->removeElement($authCode)) {
            if ($authCode->getAlipayUser() === $this) {
                $authCode->setAlipayUser(null);
            }
        }
    }

    public function getLatestAuthCode(): ?AuthCode
    {
        $lastAuthCode = $this->authCodes->last();

        return false !== $lastAuthCode ? $lastAuthCode : null;
    }

    /**
     * @return Collection<int, AlipayUserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(AlipayUserPhone $userPhone): void
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones->add($userPhone);
            $userPhone->setUser($this);
        }
    }

    public function removeUserPhone(AlipayUserPhone $userPhone): void
    {
        if ($this->userPhones->removeElement($userPhone)) {
            if ($userPhone->getUser() === $this) {
                $userPhone->setUser(null);
            }
        }
    }

    public function getLatestPhone(): ?Phone
    {
        $latestUserPhone = $this->userPhones->last();

        return (false !== $latestUserPhone) ? $latestUserPhone->getPhone() : null;
    }

    public function __toString(): string
    {
        return $this->nickName ?? $this->openId ?? 'User #' . ($this->id ?? 0);
    }
}
