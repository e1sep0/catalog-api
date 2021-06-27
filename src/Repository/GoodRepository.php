<?php

namespace App\Repository;

use App\Entity\Good;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Good|null find($id, $lockMode = null, $lockVersion = null)
 * @method Good|null findOneBy(array $criteria, array $orderBy = null)
 * @method Good[]    findAll()
 * @method Good[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Good::class);
    }

    public function getGoodsByCategory(int $categoryId)
    {
        $qb = $this->createQueryBuilder('g');

        $query = $qb
            ->join('g.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId);

        return $query->getQuery()->getResult();
    }
}
