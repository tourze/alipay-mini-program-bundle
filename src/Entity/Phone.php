<?php

namespace AlipayMiniProgramBundle\Entity;

use AlipayMiniProgramBundle\Repository\PhoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[ORM\Table(name: 'alipay_mini_program_phone')]
#[ORM\HasLifecycleCallbacks]
class Phone
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
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

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
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
}
