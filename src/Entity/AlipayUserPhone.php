<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\AlipayUserPhoneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: AlipayUserPhoneRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_user_phone')]
#[ORM\HasLifecycleCallbacks]
class AlipayUserPhone
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userPhones')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的支付宝用户'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Phone::class, inversedBy: 'userPhones')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => '关联的手机号码'])]
    private ?Phone $phone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '手机号码验证时间'])]
    private ?\DateTimeInterface $verifiedTime = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(?Phone $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getVerifiedTime(): ?\DateTimeInterface
    {
        return $this->verifiedTime;
    }

    public function setVerifiedTime(\DateTimeInterface $verifiedTime): static
    {
        $this->verifiedTime = $verifiedTime;

        return $this;
    }
}
