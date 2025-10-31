<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<MiniProgram>
 */
#[AsRepository(entityClass: MiniProgram::class)]
class MiniProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MiniProgram::class);
    }

    public function findByAppId(string $appId): ?MiniProgram
    {
        return $this->findOneBy(['appId' => $appId]);
    }

    public function findByCode(string $code): ?MiniProgram
    {
        return $this->findOneBy(['code' => $code]);
    }

    public function save(MiniProgram $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MiniProgram $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
