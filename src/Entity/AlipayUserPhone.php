<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\AlipayUserPhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AlipayUserPhoneRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_user_phone', options: ['comment' => '支付宝用户手机号关联表'])]
#[AsEntityListener]
class AlipayUserPhone implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userPhones')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的支付宝用户'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Phone::class, inversedBy: 'userPhones')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的手机号码'])]
    private ?Phone $phone = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '手机号码验证时间'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $verifiedTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): void
    {
        $this->phone = $phone;
    }

    public function getVerifiedTime(): ?\DateTimeImmutable
    {
        return $this->verifiedTime;
    }

    public function setVerifiedTime(\DateTimeInterface $verifiedTime): void
    {
        $this->verifiedTime = $verifiedTime instanceof \DateTimeImmutable ? $verifiedTime : \DateTimeImmutable::createFromInterface($verifiedTime);
    }

    public function __toString(): string
    {
        return sprintf('用户%s绑定手机%s',
            $this->user?->getNickName() ?? $this->user?->getOpenId() ?? 'unknown',
            $this->phone?->getNumber() ?? 'unknown'
        );
    }
}
