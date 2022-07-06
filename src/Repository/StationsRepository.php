<?php

namespace App\Repository;

use App\Entity\Station;
use App\Form\FilterFormType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Station>
 *
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    public function add(Station $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Station $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Stations[] Returns an array of Stations objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Stations
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getStationTypeOptions(): array
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.type')
            ->groupBy('u.type')
            ->getQuery();
        return $query->execute(array(), Query::HYDRATE_SCALAR_COLUMN);
    }

    /**
     * @param $type
     * @param $city
     * @return float|int|mixed|string
     */
    public function findStations($type, $city)
    {
        $qb = $this->createQueryBuilder('s');

        $qb = $qb
            ->join('s.location', 'l')
            ->addSelect('l')
            ->select('s.id', 's.type', 'l.City');

        if (!empty($type)) {
            $qb = $qb
                ->andWhere('s.type like :type')
                ->setParameter('type', $type);
        }
        if (!empty($city)) {
            $qb = $qb
                ->andWhere('l.City like :city')
                ->setParameter('city', $city);
        }
        return $qb->getQuery()->getResult();
    }


}
