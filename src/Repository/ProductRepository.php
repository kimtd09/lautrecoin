<?php

namespace App\Repository;

use App\Entity\Product;
use DateInterval;
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

       public function findPage(int $page): array
       {
           return $this->createQueryBuilder('p')
               ->orderBy('p.id', 'DESC')
               ->setMaxResults(12)
               ->setFirstResult(($page-1) *12)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByCategoryWithPage(int $category, int $page): ?array
       {
           return  $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
               ->orderBy('p.id', 'DESC')
               ->setParameter('category', $category)
               ->setMaxResults(12)
               ->setFirstResult(($page-1) *12)
               ->getQuery()
               ->getResult()
           ;
       }

       public function countForThisCategory($category) : int{
            return  $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult()
        ;
       }


       public function findByKeyword($value, int $page=1): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.title LIKE :val')
               ->setParameter('val', "% $value %")
               ->orderBy('p.publish_date', 'DESC')
               ->setMaxResults(12)
               ->setFirstResult(($page-1) *12)
               ->getQuery()
               ->getResult()
           ;
       }

       public function countByKeyword($keyword) : int {
        return $this->createQueryBuilder('p')
        ->select('count(p.id)')
       ->andWhere('p.title LIKE :val')
       ->setParameter('val', "% $keyword %")
       ->getQuery()
       ->getSingleScalarResult()
   ;
       }

        public function findWithFilters($minPrice, $maxPrice, $region, $department, $dateInterval, $category, $keyword, $page): array
       {
            $date = $this->createDateFromInterval($dateInterval);

            return $this->createQueryWithFilters($minPrice, $maxPrice, $region, $department, $date, $category, $keyword)
                ->orderBy('p.id', 'DESC')
                ->setMaxResults(12)
               ->setFirstResult(($page-1) *12)
               ->getQuery()
               ->getResult()
               ;
       }

       public function countForFilters($minPrice, $maxPrice, $region, $department, $dateInterval, $category, $keyword) : int {
        $date = $this->createDateFromInterval($dateInterval);
        return $this->createQueryWithFilters($minPrice, $maxPrice, $region, $department, $date, $category, $keyword)
        ->select('count(p.id)')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getSingleScalarResult()
        ;
       }

       private function createDateFromInterval($interval) {
            $date = new \DateTimeImmutable("now");
            switch ($interval) {
                case '1':
                    return $date->sub(new DateInterval("P1D"));
                case '7':
                    return $date->sub(new DateInterval("P1W"));
                case '30':
                    return $date->sub(new DateInterval("P1M"));
                default:
                    break;
            }

            return null;
       }

       private function createQueryWithFilters($minPrice, $maxPrice, $region, $department, $date, $category, $keyword) {
            $query = $this->createQueryBuilder('p');

            if($keyword) {
                $query->andWhere('p.title LIKE :val')
                ->setParameter('val', "% $keyword %");
            }

            if($category) {
                $query->andWhere('p.category = :cat')
                ->setParameter('cat', $category);
            }

            if($minPrice) {
                $query->andWhere('p.price >= :min')
                ->setParameter('min', $minPrice);
            }

            if($maxPrice) {
                $query->andWhere('p.price <= :max')
                ->setParameter('max', $maxPrice);
            }

            if($department) {
                $query->andWhere('p.department = :dep')
            ->setParameter('dep', $department);
            }
            else if($region) {
                $query->andWhere('p.region = :region')
            ->setParameter('region', $region);
            }

            if($date) {
                $query->andWhere('p.publish_date >= :date')
            ->setParameter('date', $date);
            }

        return $query;
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
