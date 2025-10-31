<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<TemplateMessage>
 */
#[AsRepository(entityClass: TemplateMessage::class)]
class TemplateMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateMessage::class);
    }

    /**
     * 查找未发送的消息
     */
    /**
     * @return TemplateMessage[]
     */
    public function findUnsentMessages(int $limit = 10): array
    {
        $result = $this->createQueryBuilder('m')
            ->andWhere('m.isSent = :isSent')
            ->setParameter('isSent', false)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        return is_array($result) ? array_filter($result, fn ($item) => $item instanceof TemplateMessage) : [];
    }

    public function save(TemplateMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TemplateMessage $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
