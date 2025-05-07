<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\TemplateMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method TemplateMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateMessage[]    findAll()
 * @method TemplateMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateMessageRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateMessage::class);
    }

    /**
     * 查找未发送的消息
     */
    public function findUnsentMessages(int $limit = 10): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.isSent = :isSent')
            ->setParameter('isSent', false)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
