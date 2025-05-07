<?php

namespace AlipayMiniProgramBundle\Repository;

use AlipayMiniProgramBundle\Entity\AlipayUserPhone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AlipayUserPhone>
 *
 * @method AlipayUserPhone|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlipayUserPhone|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlipayUserPhone[]    findAll()
 * @method AlipayUserPhone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlipayUserPhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlipayUserPhone::class);
    }
}
