<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineRandomBundle\Attribute\RandomStringColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: MiniProgramRepository::class)]
#[ORM\Table(name: 'alipay_mini_program', options: ['comment' => '支付宝小程序配置表'])]
#[AsEntityListener]
class MiniProgram implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[RandomStringColumn(length: 10)]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, nullable: true, options: ['comment' => '编码'])]
    #[Assert\Length(max: 100)]
    private ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    #[ORM\Column(length: 64, options: ['comment' => '小程序名称'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 64, options: ['comment' => '小程序 AppID'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 64)]
    private ?string $appId = null;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '应用私钥'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 2048)]
    private ?string $privateKey = null;

    #[ORM\Column(type: Types::TEXT, options: ['comment' => '支付宝公钥'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 2048)]
    private ?string $alipayPublicKey = null;

    #[ORM\Column(length: 64, nullable: true, options: ['comment' => 'AES密钥'])]
    #[Assert\Length(max: 64)]
    private ?string $encryptKey = null;

    #[ORM\Column(options: ['comment' => '是否为沙箱环境'])]
    #[Assert\Type(type: 'bool')]
    private bool $sandbox = false;

    #[ORM\Column(length: 8, options: ['comment' => '签名类型，RSA2或RSA'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 8)]
    #[Assert\Choice(choices: ['RSA', 'RSA2'])]
    private string $signType = 'RSA2';

    #[ORM\Column(length: 128, options: ['comment' => '支付宝网关地址'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 128)]
    #[Assert\Url]
    private string $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '授权回调地址'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $authRedirectUrl = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '备注说明'])]
    #[Assert\Length(max: 255)]
    private ?string $remark = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    public function getAlipayPublicKey(): ?string
    {
        return $this->alipayPublicKey;
    }

    public function setAlipayPublicKey(string $alipayPublicKey): void
    {
        $this->alipayPublicKey = $alipayPublicKey;
    }

    public function getEncryptKey(): ?string
    {
        return $this->encryptKey;
    }

    public function setEncryptKey(?string $encryptKey): void
    {
        $this->encryptKey = $encryptKey;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function setSandbox(bool $sandbox): void
    {
        $this->sandbox = $sandbox;
    }

    public function getSignType(): string
    {
        return $this->signType;
    }

    public function setSignType(string $signType): void
    {
        $this->signType = $signType;
    }

    public function getGatewayUrl(): string
    {
        return $this->sandbox ? 'https://openapi.alipaydev.com/gateway.do' : $this->gatewayUrl;
    }

    public function setGatewayUrl(string $gatewayUrl): void
    {
        $this->gatewayUrl = $gatewayUrl;
    }

    public function getAuthRedirectUrl(): ?string
    {
        return $this->authRedirectUrl;
    }

    public function setAuthRedirectUrl(?string $authRedirectUrl): void
    {
        $this->authRedirectUrl = $authRedirectUrl;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->appId ?? 'Alipay MiniProgram #' . ($this->id ?? 0);
    }
}
