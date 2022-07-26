<?php

namespace App\Repository;

use App\Entity\Article;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByKeyword($keyword): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.title LIKE :keyword')
            ->orWhere('a.body LIKE :keyword')
            ->setParameter('keyword', "%{$keyword}%")
            ->orderBy('a.title', 'ASC')
            ->addOrderBy('a.published_at', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByPublishedAtIsNull(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.published_at IS NULL')
            ->orderBy('a.title', 'ASC')
            ->addOrderBy('a.body', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByPublishedAtBefore(DateTime $date): array
    {
        $interval = DateInterval::createFromDateString('1 day');
        $date = $date->add($interval);

        return $this->createQueryBuilder('a')
            ->andWhere('a.published_at <= :date')
            ->setParameter('date', $date->format('Y-m-d 00:00:00'))
            ->orderBy('a.published_at', 'DESC')
            ->addOrderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }


       /**
        * @return Article[] Returns an array of Article objects
        */
       public function findNLast(int $n): array
       {
           return $this->createQueryBuilder('a')
               ->orderBy('a.published_at', 'DESC')
               ->setMaxResults($n)
               ->getQuery()
               ->getResult()
           ;
       }


    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
