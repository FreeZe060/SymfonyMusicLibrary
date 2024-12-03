<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

   /**
    * @return Song[] Returns an array of Song objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('s.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneBySomeField($value): ?Song
   {
       return $this->createQueryBuilder('s')
           ->andWhere('s.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function findByDurationInterval(int $minDuration, int $maxDuration)
    {
        return $this->createQueryBuilder('s')
            ->where('s.length >= :minDuration')
            ->andWhere('s.length <= :maxDuration')
            ->setParameter('minDuration', $minDuration)
            ->setParameter('maxDuration', $maxDuration)
            ->getQuery()
            ->getResult();
    }
}
