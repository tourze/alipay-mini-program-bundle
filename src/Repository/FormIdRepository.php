<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method FormId|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormId|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormId[]    findAll()
 * @method FormId[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormIdRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormId::class);
    }

    /**
     * 获取一个可用的formId
     *
     * 规则:
     * 1. 未过期
     * 2. 使用次数小于3次
     */
    public function findAvailableFormId(MiniProgram $miniProgram, User $user): ?FormId
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.miniProgram = :miniProgram')
            ->andWhere('f.user = :user')
            ->andWhere('f.usedCount < 3')
            ->andWhere('f.expireTime > :now')
            ->setParameter('miniProgram', $miniProgram)
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('f.expireTime', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 清理过期的formId
     *
     * 清理规则:
     * 1. 过期时间超过7天
     * 2. 从未使用过(usedCount = 0)
     *
     * 注意: 只要使用过的formId都会被保留,不会被清理
     */
    public function cleanExpiredFormIds(): int
    {
        $expireDate = new \DateTime('-7 days');

        return $this->createQueryBuilder('f')
            ->delete()
            ->where('f.expireTime <= :expireDate')
            ->andWhere('f.usedCount = 0')
            ->setParameter('expireDate', $expireDate)
            ->getQuery()
            ->execute();
    }
}
