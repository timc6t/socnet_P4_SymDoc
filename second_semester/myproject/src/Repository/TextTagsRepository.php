<?php

namespace App\Repository;

use App\Entity\TextTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TextTags>
 *
 * @method TextTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextTags[]    findAll()
 * @method TextTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextTagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextTags::class);
    }

//    /**
//     * @return TextTags[] Returns an array of TextTags objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TextTags
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
