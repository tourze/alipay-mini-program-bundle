<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\PhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_phone', options: ['comment' => '手机号码表'])]
#[AsEntityListener]
class Phone implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\Column(length: 32, unique: true, options: ['comment' => '手机号码'])]
    #[Assert\NotNull]
    #[Assert\Length(max: 32)]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: '请输入有效的手机号码')]
    private ?string $number = null;

    /**
     * @var Collection<int, AlipayUserPhone>
     */
    #[ORM\OneToMany(targetEntity: AlipayUserPhone::class, mappedBy: 'phone')]
    private Collection $userPhones;

    public function __construct()
    {
        $this->userPhones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * @return Collection<int, AlipayUserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(AlipayUserPhone $userPhone): void
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones->add($userPhone);
            $userPhone->setPhone($this);
        }
    }

    public function removeUserPhone(AlipayUserPhone $userPhone): void
    {
        if ($this->userPhones->removeElement($userPhone)) {
            if ($userPhone->getPhone() === $this) {
                $userPhone->setPhone(null);
            }
        }
    }

    /**
     * 获取绑定用户数量（虚拟字段，用于EasyAdmin显示）
     */
    public function getUserPhonesCount(): int
    {
        return $this->userPhones->count();
    }

    public function __toString(): string
    {
        return $this->number ?? 'Phone #' . ($this->id ?? 0);
    }
}
