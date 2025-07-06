<?php

namespace App\Repository;

use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    //    /**
    //     * @return Module[] Returns an array of Module objects
    //     */
       public function findByNoModules($value): array
       {
            $em = $this->getEntityManager();

            // Sous-requête : récupérer les IDs des modules déjà associés à la session
            $subQb = $em->createQueryBuilder();
            $subQb->select('IDENTITY(p.module)')
                ->from('App\Entity\Programme', 'p')
                ->where('p.session = :sessionId');

            // Requête principale : récupérer les modules non associés à cette session
            $qb = $em->createQueryBuilder();
            $qb->select('m')
                ->from('App\Entity\Module', 'm')
                ->where($qb->expr()->notIn('m.id', $subQb->getDQL()))
                ->setParameter('sessionId', $value)
                ->orderBy('m.id');

            return $qb->getQuery()->getResult();
       }
}
