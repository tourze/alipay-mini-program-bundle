<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\FormIdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: FormIdRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_form_id')]
#[ORM\HasLifecycleCallbacks]
class FormId
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

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

    /**
     * formId
     */
    #[ORM\Column(length: 64, options: ['comment' => 'formId'])]
    private ?string $formId = null;

    /**
     * 过期时间
     */
    #[IndexColumn]
    #[ORM\Column(type: 'datetime', options: ['comment' => '过期时间'])]
    private ?\DateTimeInterface $expireTime = null;

    /**
     * 使用次数，达到3次即视为已使用
     */
    #[IndexColumn]
    #[ORM\Column(type: 'integer', options: ['comment' => '使用次数，达到3次即视为已使用', 'default' => 0])]
    private int $usedCount = 0;

    use TimestampableAware;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(\DateTimeInterface $expireTime): static
    {
        $this->expireTime = $expireTime;

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
}
