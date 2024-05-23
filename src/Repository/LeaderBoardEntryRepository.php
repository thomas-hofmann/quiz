<?php

namespace App\Repository;

use App\Entity\LeaderBoardEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeaderBoardEntry>
 *
 * @method LeaderBoardEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeaderBoardEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeaderBoardEntry[]    findAll()
 * @method LeaderBoardEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaderBoardEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaderBoardEntry::class);
    }

//    /**
//     * @return LeaderBoardEntry[] Returns an array of LeaderBoardEntry objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LeaderBoardEntry
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
