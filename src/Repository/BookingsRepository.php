<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function add(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


//    /**
//     * @return Bookings[] Returns an array of Bookings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bookings
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getActiveBookings($station)
    {
        $sql = "SELECT b FROM App\Entity\Booking b INNER JOIN App\Entity\Station s WHERE s.id = ?1 AND b.station = s AND b.charge_end > ?2 ORDER BY b.charge_start";
        return $this->getEntityManager()->createQuery($sql)->setParameter(1, $station)->setParameter(2, new \DateTimeImmutable())->getResult();
    }

    public function getUserBookings($user)
    {
        $sql = "SELECT b FROM App\Entity\Booking b INNER JOIN App\Entity\Car c WHERE c = b.car AND c.user = ?1 AND b.charge_end > ?2 ORDER BY b.charge_start";
        return $this->getEntityManager()->createQuery($sql)->setParameter(1, $user)->setParameter(2,  new \DateTimeImmutable())->getResult();
    }

    public function getOverlappingBookings($station, $start, $end)
    {
        $sql = "SELECT b FROM App\Entity\Booking b WHERE b.station = ?3 AND ((b.charge_start <= ?1 AND b.charge_end >= ?1) OR (b.charge_start <= ?2 AND b.charge_end >= ?2) OR (b.charge_start >= ?1 AND b.charge_end <= ?2))";
        return $this->getEntityManager()->createQuery($sql)->setParameter(1, $start)->setParameter(2,  $end)->setParameter(3, $station)->getResult();
    }

    public function getCarOverlap($car, $start, $end)
    {
        $sql = "SELECT b FROM App\Entity\Booking b WHERE b.car = ?3 AND ((b.charge_start <= ?1 AND b.charge_end >= ?1) OR (b.charge_start <= ?2 AND b.charge_end >= ?2) OR (b.charge_start >= ?1 AND b.charge_end <= ?2))";
        return $this->getEntityManager()->createQuery($sql)->setParameter(1, $start)->setParameter(2,  $end)->setParameter(3, $car)->getResult();
    }
}
