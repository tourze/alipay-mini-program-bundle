<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\FormId;
use AlipayMiniProgramBundle\Entity\MiniProgram;
use AlipayMiniProgramBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<FormId>
 */
#[AsRepository(entityClass: FormId::class)]
class FormIdRepository extends ServiceEntityRepository
{
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
        $result = $this->createQueryBuilder('f')
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
            ->getOneOrNullResult()
        ;

        return $result instanceof FormId ? $result : null;
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

        $result = $this->createQueryBuilder('f')
            ->delete()
            ->where('f.expireTime <= :expireDate')
            ->andWhere('f.usedCount = 0')
            ->setParameter('expireDate', $expireDate)
            ->getQuery()
            ->execute()
        ;

        return is_numeric($result) ? (int) $result : 0;
    }

    public function save(FormId $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FormId $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
