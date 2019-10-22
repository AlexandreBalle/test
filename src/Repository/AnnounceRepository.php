<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Announce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announce[]    findAll()
 * @method Announce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announce::class);
    }

    /**
     * @param $value
     * @return Announce[] Returns an array of Announce objects
     */
    public function findForSearch($value)
    {
        $search = $this->createQueryBuilder('a')
            ->where('a.enable = true')
            ->orderBy('a.id', 'ASC')
        ;

        if ($value['type'] != null) {
            $search->innerJoin('a.vehicle', 'v')
                   ->andWhere('v.type = :type')
                   ->setParameter('type', $value['type'])
            ;
        }

        if ($value['minPrice'] != null) {
            $search->andWhere('a.price >= :priceMini')
                   ->setParameter('priceMini', $value['minPrice'])
            ;
        }
        if ($value['maxPrice'] != null) {
            $search->andWhere('a.price <= :maxPrice')
                   ->setParameter('maxPrice', $value['maxPrice'])
            ;
        }

        return $search->getQuery()->getResult();
    }

    /**
     * @param $value
     * @return Announce[] Returns an array of Announce objects
     */
    public function findForSearchSwipe($value)
    {
        $search = $this->createQueryBuilder('a')
                       ->orderBy('a.id', 'ASC')
        ;

        if ($value['type'] != null) {
            $search->innerJoin('a.vehicle', 'v')
                   ->andWhere('v.type = :type')
                   ->setParameter('type', $value['type'])
            ;
        }
        if ($value['minPrice'] != null) {
            $search->andWhere('a.price >= :priceMini')
                   ->setParameter('priceMini', $value['minPrice'])
            ;
        }
        if ($value['maxPrice'] != null) {
            $search->andWhere('a.price <= :maxPrice')
                   ->setParameter('maxPrice', $value['maxPrice'])
            ;
        }

        return $search->getQuery()->getResult();
    }

    /**
     * @param $type
     * @return Announce[] Returns an array of Announce objects
     */
    public function findByVehicleType($type)
    {
        return $this->createQueryBuilder('a')
                    ->innerJoin('a.vehicle', 'v')
                    ->where('a.enable = true')
                    ->andWhere('v.type = :type')
                    ->setParameter('type', $type)
                    ->orderBy('a.id', 'ASC')
                    ->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Announce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
