<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: TemplateMessageRepository::class)]
#[ORM\Table(name: 'amptm_template_message')]
class TemplateMessage
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

    #[ORM\ManyToOne(targetEntity: MiniProgram::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MiniProgram $miniProgram;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $toUser;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板ID'])]
    private string $templateId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '接收者的openId'])]
    private string $toOpenId;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '页面路径'])]
    private ?string $page = null;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '模板数据'])]
    private array $data = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已发送', 'default' => false])]
    private bool $isSent = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeImmutable $sentTime = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '发送结果'])]
    private ?string $sendResult = null;

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

    public function getMiniProgram(): MiniProgram
    {
        return $this->miniProgram;
    }

    public function setMiniProgram(MiniProgram $miniProgram): self
    {
        $this->miniProgram = $miniProgram;

        return $this;
    }

    public function getToUser(): User
    {
        return $this->toUser;
    }

    public function setToUser(User $toUser): self
    {
        $this->toUser = $toUser;

        return $this;
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    public function setTemplateId(string $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getSentTime(): ?\DateTimeImmutable
    {
        return $this->sentTime;
    }

    public function setSentTime(?\DateTimeImmutable $sentTime): self
    {
        $this->sentTime = $sentTime;

        return $this;
    }

    public function getSendResult(): ?string
    {
        return $this->sendResult;
    }

    public function setSendResult(?string $sendResult): self
    {
        $this->sendResult = $sendResult;

        return $this;
    }

    public function getToOpenId(): string
    {
        return $this->toOpenId;
    }

    public function setToOpenId(string $toOpenId): self
    {
        $this->toOpenId = $toOpenId;

        return $this;
    }
}
