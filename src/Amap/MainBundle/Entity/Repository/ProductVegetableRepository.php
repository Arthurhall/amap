<?php

namespace Amap\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class ProductVegetableRepository extends EntityRepository
{
	public function findAllWithAvgPrices()
	{
		$qb = $this->createQueryBuilder('pv')
			->addSelect('p')
			->addSelect('q')
			->addSelect('i')
			
            ->leftJoin('pv.products', 'p')
			->leftJoin('p.quantityType', 'q')
			->leftJoin('pv.image', 'i')
			
            ->orderBy('pv.title', 'ASC')
		;
        
        $r = $qb->getQuery()
            ->getResult();
		return $r;
	}
	
}
