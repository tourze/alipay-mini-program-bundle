<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\MiniProgram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<MiniProgram>
 *
 * @method MiniProgram|null find($id, $lockMode = null, $lockVersion = null)
 * @method MiniProgram|null findOneBy(array $criteria, array $orderBy = null)
 * @method MiniProgram[]    findAll()
 * @method MiniProgram[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
}
