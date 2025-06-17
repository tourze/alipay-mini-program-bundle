<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\MiniProgramRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineRandomBundle\Attribute\RandomStringColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;

#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: MiniProgramRepository::class)]
#[ORM\Table(name: 'alipay_mini_program')]
#[ORM\HasLifecycleCallbacks]
class MiniProgram
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[RandomStringColumn(length: 10)]
    #[FormField(title: '编码', order: -1)]
    #[Keyword]
    #[ListColumn(order: -1)]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, nullable: true, options: ['comment' => '编码'])]
    private ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 小程序名称
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 64, options: ['comment' => '小程序名称'])]
    private ?string $name = null;

    /**
     * 支付宝小程序的 AppID
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 64, options: ['comment' => '小程序 AppID'])]
    private ?string $appId = null;

    /**
     * 支付宝小程序的私钥
     */
    #[FormField]
    #[ORM\Column(type: 'text', options: ['comment' => '应用私钥'])]
    private ?string $privateKey = null;

    /**
     * 支付宝公钥
     */
    #[FormField]
    #[ORM\Column(type: 'text', options: ['comment' => '支付宝公钥'])]
    private ?string $alipayPublicKey = null;

    /**
     * AES密钥
     */
    #[FormField]
    #[ORM\Column(length: 64, nullable: true, options: ['comment' => 'AES密钥'])]
    private ?string $encryptKey = null;

    /**
     * 是否是沙箱环境
     */
    #[ListColumn]
    #[FormField]
    #[BoolColumn]
    #[ORM\Column(options: ['comment' => '是否为沙箱环境'])]
    private bool $sandbox = false;

    /**
     * 签名类型，RSA2或RSA
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 8, options: ['comment' => '签名类型，RSA2或RSA'])]
    private string $signType = 'RSA2';

    /**
     * 支付宝网关
     */
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 128, options: ['comment' => '支付宝网关地址'])]
    private string $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    /**
     * 授权回调地址
     */
    #[FormField]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $authRedirectUrl = null;

    /**
     * 备注说明
     */
    #[FormField]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '备注说明'])]
    private ?string $remark = null;

    use TimestampableAware;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): static
    {
        $this->appId = $appId;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $privateKey): static
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getAlipayPublicKey(): ?string
    {
        return $this->alipayPublicKey;
    }

    public function setAlipayPublicKey(string $alipayPublicKey): static
    {
        $this->alipayPublicKey = $alipayPublicKey;

        return $this;
    }

    public function getEncryptKey(): ?string
    {
        return $this->encryptKey;
    }

    public function setEncryptKey(?string $encryptKey): static
    {
        $this->encryptKey = $encryptKey;

        return $this;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function setSandbox(bool $sandbox): static
    {
        $this->sandbox = $sandbox;

        return $this;
    }

    public function getSignType(): string
    {
        return $this->signType;
    }

    public function setSignType(string $signType): static
    {
        $this->signType = $signType;

        return $this;
    }

    public function getGatewayUrl(): string
    {
        return $this->sandbox ? 'https://openapi.alipaydev.com/gateway.do' : $this->gatewayUrl;
    }

    public function setGatewayUrl(string $gatewayUrl): static
    {
        $this->gatewayUrl = $gatewayUrl;

        return $this;
    }

    public function getAuthRedirectUrl(): ?string
    {
        return $this->authRedirectUrl;
    }

    public function setAuthRedirectUrl(?string $authRedirectUrl): static
    {
        $this->authRedirectUrl = $authRedirectUrl;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;

        return $this;
    }
}
