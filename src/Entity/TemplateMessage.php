<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\TemplateMessageRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: TemplateMessageRepository::class)]
#[ORM\Table(name: 'amptm_template_message', options: ['comment' => '支付宝小程序模板消息表'])]
#[AsEntityListener]
class TemplateMessage implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: MiniProgram::class)]
    #[ORM\JoinColumn(nullable: false)]
    private MiniProgram $miniProgram;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $toUser;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $templateId;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '接收者的openId'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $toOpenId;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '页面路径'])]
    #[Assert\Length(max: 100)]
    private ?string $page = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '模板数据'])]
    #[Assert\Type(type: 'array')]
    private array $data = [];

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已发送', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $isSent = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    #[Assert\Type(type: '\DateTimeImmutable')]
    private ?\DateTimeImmutable $sentTime = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '发送结果'])]
    #[Assert\Length(max: 100)]
    private ?string $sendResult = null;

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

    public function getToUser(): User
    {
        return $this->toUser;
    }

    public function setToUser(User $toUser): void
    {
        $this->toUser = $toUser;
    }

    public function getTemplateId(): string
    {
        return $this->templateId;
    }

    public function setTemplateId(string $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): void
    {
        $this->page = $page;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function isSent(): bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): void
    {
        $this->isSent = $isSent;
    }

    public function getSentTime(): ?\DateTimeImmutable
    {
        return $this->sentTime;
    }

    public function setSentTime(?\DateTimeImmutable $sentTime): void
    {
        $this->sentTime = $sentTime;
    }

    public function getSendResult(): ?string
    {
        return $this->sendResult;
    }

    public function setSendResult(?string $sendResult): void
    {
        $this->sendResult = $sendResult;
    }

    public function getToOpenId(): string
    {
        return $this->toOpenId;
    }

    public function setToOpenId(string $toOpenId): void
    {
        $this->toOpenId = $toOpenId;
    }

    /**
     * 获取模板数据预览（虚拟字段，用于EasyAdmin显示）
     */
    public function getDataPreview(): string
    {
        $jsonResult = json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return false !== $jsonResult ? $jsonResult : '';
    }

    public function __toString(): string
    {
        return sprintf('模板消息:%s - 接收者:%s',
            $this->templateId,
            $this->toUser->getNickName() ?? $this->toOpenId
        );
    }
}
