<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Enum\AlipayAuthScope;
use AlipayMiniProgramBundle\Repository\AuthCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: AuthCodeRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_auth_code', options: ['comment' => '支付宝小程序授权码表'])]
class AuthCode implements \Stringable
{
    use TimestampableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL', options: ['comment' => '关联的支付宝用户'])]
    private ?User $alipayUser = null;

    #[ORM\Column(length: 128, options: ['comment' => '授权码，用于换取访问令牌'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 128)]
    private ?string $authCode = null;

    #[ORM\Column(length: 32, enumType: AlipayAuthScope::class, options: ['comment' => '授权范围'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [AlipayAuthScope::class, 'cases'])]
    private ?AlipayAuthScope $scope = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => '授权请求中的状态值，用于防止CSRF攻击'])]
    #[Assert\Length(max: 32)]
    private ?string $state = null;

    #[ORM\Column(length: 64, options: ['comment' => '支付宝用户的 user_id，已废弃'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private ?string $userId = null;

    #[ORM\Column(length: 64, options: ['comment' => '支付宝用户的 open_id'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private ?string $openId = null;

    #[ORM\Column(length: 128, options: ['comment' => '访问令牌'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 128)]
    private ?string $accessToken = null;

    #[ORM\Column(length: 128, options: ['comment' => '刷新令牌'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 128)]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '访问令牌的有效时间，单位：秒'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 1)]
    private ?int $expiresIn = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '刷新令牌的有效时间，单位：秒'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 1)]
    private ?int $reExpiresIn = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '授权开始时间'])]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $authStart = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '签名'])]
    #[Assert\Length(max: 128)]
    private ?string $sign = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlipayUser(): ?User
    {
        return $this->alipayUser;
    }

    public function setAlipayUser(?User $alipayUser): void
    {
        $this->alipayUser = $alipayUser;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getScope(): ?AlipayAuthScope
    {
        return $this->scope;
    }

    public function setScope(AlipayAuthScope $scope): void
    {
        $this->scope = $scope;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): void
    {
        $this->openId = $openId;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(int $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    public function getReExpiresIn(): ?int
    {
        return $this->reExpiresIn;
    }

    public function setReExpiresIn(int $reExpiresIn): void
    {
        $this->reExpiresIn = $reExpiresIn;
    }

    public function getAuthStart(): ?\DateTimeImmutable
    {
        return $this->authStart;
    }

    public function setAuthStart(\DateTimeInterface $authStart): void
    {
        $this->authStart = $authStart instanceof \DateTimeImmutable ? $authStart : \DateTimeImmutable::createFromInterface($authStart);
    }

    public function getSign(): ?string
    {
        return $this->sign;
    }

    public function setSign(?string $sign): void
    {
        $this->sign = $sign;
    }

    public function __toString(): string
    {
        return sprintf('授权码:%s - 用户:%s',
            substr($this->authCode ?? '', 0, 8) . '...',
            $this->alipayUser?->getNickName() ?? $this->openId ?? 'unknown'
        );
    }
}
