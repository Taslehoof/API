<?php

namespace App\Repository;

use App\Entity\ProductoFoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductoFoto>
 *
 * @method ProductoFoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductoFoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductoFoto[]    findAll()
 * @method ProductoFoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoFotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductoFoto::class);
    }

    //    /**
    //     * @return ProductoFoto[] Returns an array of ProductoFoto objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ProductoFoto
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
