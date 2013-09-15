<?php

namespace Amap\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class PermanenceRepository extends EntityRepository
{
    public function findAllWithUsers()
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('u')
            
            ->InnerJoin('p.users', 'u')
            
            ->where('p.deliveryDate >= :now')
			->setParameter('now', new \DateTime('now'))
        ;
        
        $r = $qb->getQuery()
        ->getResult();
    
        return $r;
    }
}
