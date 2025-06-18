<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\FormIdRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: FormIdRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_form_id', options: ['comment' => '支付宝小程序formId表'])]
#[AsEntityListener]
class FormId implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    /**
     * 关联的小程序
     */
    #[ORM\ManyToOne(targetEntity: MiniProgram::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?MiniProgram $miniProgram = null;

    /**
     * 关联的用户
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 64, options: ['comment' => 'formId'])]
    private ?string $formId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '过期时间'])]
    private ?\DateTimeImmutable $expireTime = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '使用次数，达到3次即视为已使用', 'default' => 0])]
    private int $usedCount = 0;

    use TimestampableAware;
    use BlameableAware;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMiniProgram(): ?MiniProgram
    {
        return $this->miniProgram;
    }

    public function setMiniProgram(?MiniProgram $miniProgram): static
    {
        $this->miniProgram = $miniProgram;

        return $this;
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

    public function getFormId(): ?string
    {
        return $this->formId;
    }

    public function setFormId(string $formId): static
    {
        $this->formId = $formId;

        return $this;
    }

    public function getExpireTime(): ?\DateTimeImmutable
    {
        return $this->expireTime;
    }

    public function setExpireTime(\DateTimeInterface $expireTime): static
    {
        $this->expireTime = $expireTime instanceof \DateTimeImmutable ? $expireTime : \DateTimeImmutable::createFromInterface($expireTime);

        return $this;
    }

    public function getUsedCount(): int
    {
        return $this->usedCount;
    }

    public function setUsedCount(int $usedCount): static
    {
        $this->usedCount = $usedCount;

        return $this;
    }

    public function incrementUsedCount(): static
    {
        ++$this->usedCount;

        return $this;
    }

    public function isUsed(): bool
    {
        return $this->usedCount >= 3;
    }

    public function __toString(): string
    {
        return sprintf('FormID:%s - 用户:%s',
            substr($this->formId ?? '', 0, 8) . '...',
            $this->user?->getNickName() ?? $this->user?->getOpenId() ?? 'unknown'
        );
    }
}
