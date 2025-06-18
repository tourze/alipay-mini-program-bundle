<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use AlipayMiniProgramBundle\Repository\AuthCodeRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AuthCodeRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_auth_code', options: ['comment' => '支付宝小程序授权码表'])]
#[AsEntityListener]
class AuthCode implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL', options: ['comment' => '关联的支付宝用户'])]
    private ?User $alipayUser = null;

    #[ORM\Column(length: 128, options: ['comment' => '授权码，用于换取访问令牌'])]
    private ?string $authCode = null;

    #[ORM\Column(length: 32, enumType: AlipayAuthScope::class, options: ['comment' => '授权范围'])]
    private ?AlipayAuthScope $scope = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '授权请求中的状态值，用于防止CSRF攻击'])]
    private ?string $state = null;

    #[ORM\Column(length: 64, options: ['comment' => '支付宝用户的 user_id，已废弃'])]
    private ?string $userId = null;

    #[ORM\Column(length: 64, options: ['comment' => '支付宝用户的 open_id'])]
    private ?string $openId = null;

    #[ORM\Column(length: 128, options: ['comment' => '访问令牌'])]
    private ?string $accessToken = null;

    #[ORM\Column(length: 128, options: ['comment' => '刷新令牌'])]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '访问令牌的有效时间，单位：秒'])]
    private ?int $expiresIn = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '刷新令牌的有效时间，单位：秒'])]
    private ?int $reExpiresIn = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '授权开始时间'])]
    private ?\DateTimeImmutable $authStart = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '签名'])]
    private ?string $sign = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    use TimestampableAware;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlipayUser(): ?User
    {
        return $this->alipayUser;
    }

    public function setAlipayUser(?User $alipayUser): static
    {
        $this->alipayUser = $alipayUser;

        return $this;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(string $authCode): static
    {
        $this->authCode = $authCode;

        return $this;
    }

    public function getScope(): ?AlipayAuthScope
    {
        return $this->scope;
    }

    public function setScope(AlipayAuthScope $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): static
    {
        $this->openId = $openId;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(int $expiresIn): static
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    public function getReExpiresIn(): ?int
    {
        return $this->reExpiresIn;
    }

    public function setReExpiresIn(int $reExpiresIn): static
    {
        $this->reExpiresIn = $reExpiresIn;

        return $this;
    }

    public function getAuthStart(): ?\DateTimeImmutable
    {
        return $this->authStart;
    }

    public function setAuthStart(\DateTimeInterface $authStart): static
    {
        $this->authStart = $authStart instanceof \DateTimeImmutable ? $authStart : \DateTimeImmutable::createFromInterface($authStart);

        return $this;
    }

    public function getSign(): ?string
    {
        return $this->sign;
    }

    public function setSign(?string $sign): static
    {
        $this->sign = $sign;

        return $this;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function __toString(): string
    {
        return sprintf('授权码:%s - 用户:%s',
            substr($this->authCode ?? '', 0, 8) . '...',
            $this->alipayUser?->getNickName() ?? $this->openId ?? 'unknown'
        );
    }
}
