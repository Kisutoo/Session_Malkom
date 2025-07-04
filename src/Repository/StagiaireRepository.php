<?php

namespace App\Repository;

use App\Entity\Stagiaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stagiaire>
 */
class StagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaire::class);
    }

       /**
        * @return Stagiaire[] Returns an array of Stagiaire objects
        */
       public function findByNoSession($value): array
       {
            $em = $this->getEntityManager();

            // Sous-requête : récupérer les IDs des stagiaires inscrits à la session
            $subQb = $em->createQueryBuilder();
            $subQb->select('s2.id')
                ->from('App\Entity\Stagiaire', 's2')
                ->leftJoin('s2.sessions', 'se2')
                ->where('se2.id = :id');

            // Requête principale : récupérer les stagiaires NON inscrits à cette session
            $qb = $em->createQueryBuilder();
            $qb->select('st')
                ->from('App\Entity\Stagiaire', 'st')
                ->where($qb->expr()->notIn('st.id', $subQb->getDQL()))
                ->setParameter('id', $value)
                ->orderBy('st.nom');

            return $qb->getQuery()->getResult();
       }

       public function findOneById($id): ?Stagiaire
       {
           return $this->createQueryBuilder('s')
               ->andWhere('s.id = :val')
               ->setParameter('val', $id)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

       public function findBySession($id): array
       {
            $em = $this->getEntityManager();

            // Sous-requête : récupérer les IDs des stagiaires inscrits à la session
            $subQb = $em->createQueryBuilder();
            $subQb->select('s2.id')
                ->from('App\Entity\Stagiaire', 's2')
                ->leftJoin('s2.sessions', 'se2')
                ->where('se2.id = :id');

            // Requête principale : récupérer les stagiaires NON inscrits à cette session
            $qb = $em->createQueryBuilder();
            $qb->select('st')
                ->from('App\Entity\Stagiaire', 'st')
                ->where($qb->expr()->In('st.id', $subQb->getDQL()))
                ->setParameter('id', $id)
                ->orderBy('st.nom');

            return $qb->getQuery()->getResult();
       }
}
