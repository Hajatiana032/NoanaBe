<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * todo Search a product
     *
     * @param [type] $value
     * @return void
     */
    public function search($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('MATCH_AGAINST(p.title, p.description) AGAINST(:value boolean) > 0')
            ->setParameter('value', '%' . $value . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * todo Find similar product
     *
     * @param [type] $value
     * @return void
     */
    public function like(string $category, int $id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = :category')
            ->andWhere('p.id != :id')
            ->setParameter('category', $category)
            ->setParameter('id', $id)
            // ->orderBy('RAND()')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
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

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
