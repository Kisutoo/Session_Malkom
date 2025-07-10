<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

       /**
        * @return Session[] Returns an array of Session objects
        */
       public function findSessionsByStagiaire($id): array
       {
            $em = $this->getEntityManager();

            $subQb = $em->createQueryBuilder();
            $subQb->select('s2.id')
                ->from('App\Entity\Session', 's2')
                ->leftJoin('s2.stagiaires', 'se2')
                ->where('se2.id = :id');

            $qb = $em->createQueryBuilder();
            $qb->select('st')
                ->from('App\Entity\Session', 'st')
                ->where($qb->expr()->In('st.id', $subQb->getDQL()))
                ->setParameter('id', $id)
                ->orderBy('st.nomSession');

            return $qb->getQuery()->getResult();
       }

       public function findSessionsByFormateur($id): array
       {
            $em = $this->getEntityManager();

            $subQb = $em->createQueryBuilder();
            $subQb->select('s2.id')
                ->from('App\Entity\Session', 's2')
                ->leftJoin('s2.formateur', 'se2')
                ->where('se2.id = :id');

            $qb = $em->createQueryBuilder();
            $qb->select('st')
                ->from('App\Entity\Session', 'st')
                ->where($qb->expr()->In('st.id', $subQb->getDQL()))
                ->setParameter('id', $id)
                ->orderBy('st.nomSession');

            return $qb->getQuery()->getResult();
       }
    
    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
