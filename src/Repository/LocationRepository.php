<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Announce;
use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function getLocationPast($idUser)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l.startDate, l.endDate, a.city, a.address, a.price, v.brand, v.model, v.matriculation,
            DATE_DIFF(l.endDate, l.startDate) AS dateDiff, l.returned_at')
            ->innerJoin(Announce::class, 'a', Join::WITH, 'a.id = l.announce')
            ->innerJoin(Vehicle::class, 'v', Join::WITH, 'v.id = a.vehicle')
            ->where('l.returned = true')
            ->andWhere('l.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('l.startDate', 'desc');
        return $qb->getQuery()->getArrayResult();
    }

    public function getLocationFutur($idUser)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l.startDate, l.endDate, a.city, a.address, a.price, v.brand, v.model,
             v.matriculation, DATE_DIFF(l.endDate, l.startDate) AS dateDiff')
            ->innerJoin(Announce::class, 'a', Join::WITH, 'a.id = l.announce')
            ->innerJoin(Vehicle::class, 'v', Join::WITH, 'v.id = a.vehicle')
            ->where('l.startDate > CURRENT_DATE()')
            ->andWhere('l.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('l.startDate', 'desc');
        return $qb->getQuery()->getArrayResult();
    }

    public function getLocationDate($idUser)
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l.id as id, l.startDate, l.endDate, a.city, a.address, a.price, v.brand, v.model,
             DATE_DIFF(l.endDate, l.startDate) AS dateDiff, l.returned')
            ->innerJoin(Announce::class, 'a', Join::WITH, 'a.id = l.announce')
            ->innerJoin(Vehicle::class, 'v', Join::WITH, 'v.id = a.vehicle')
            ->where('CURRENT_DATE() > l.startDate AND l.returned != true')
            ->orWhere('CURRENT_DATE() BETWEEN l.startDate AND l.endDate AND l.returned = true')
            ->andWhere('l.user = :idUser')
            ->setParameter('idUser', $idUser)
            ->orderBy('l.startDate', 'desc');
        return $qb->getQuery()->getArrayResult();
    }

    // /**
    //  * @return Location[] Returns an array of Location objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Location
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
