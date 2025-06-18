<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\PhoneRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_phone', options: ['comment' => '手机号码表'])]
#[AsEntityListener]
class Phone implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(length: 32, unique: true, options: ['comment' => '手机号码'])]
    private ?string $number = null;

    /**
     * @var Collection<int, AlipayUserPhone>
     */
    #[ORM\OneToMany(targetEntity: AlipayUserPhone::class, mappedBy: 'phone')]
    private Collection $userPhones;

    use TimestampableAware;

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

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, AlipayUserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(AlipayUserPhone $userPhone): static
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones->add($userPhone);
            $userPhone->setPhone($this);
        }

        return $this;
    }

    public function removeUserPhone(AlipayUserPhone $userPhone): static
    {
        if ($this->userPhones->removeElement($userPhone)) {
            if ($userPhone->getPhone() === $this) {
                $userPhone->setPhone(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->number ?? 'Phone #' . ($this->id ?? 0);
    }
}
