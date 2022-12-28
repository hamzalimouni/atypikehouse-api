<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Notification[] Returns an array of Notification objects
     */
    public function findUserNotifications($user): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('( n.user = :user OR n.user is null ) AND n.created_at > :date AND n.type NOT IN (:hhh)')
            ->setParameter('user', $user->getId())
            ->setParameter('date', $user->getCreatedAt())
            ->setParameter('hhh', ['NEW', 'EDIT', 'DELETE'])
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findProprietaireNotifications($user): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('( n.user = :user OR n.user is null ) AND n.created_at > :date')
            ->setParameter('user', $user->getId())
            ->setParameter('date', $user->getCreatedAt())
            ->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Notification
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
