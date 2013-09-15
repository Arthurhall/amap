<?php

namespace Amap\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Tools\Pagination\Paginator;


class PanierAddonRepository extends EntityRepository
{
	public function findWithProducts($id)
	{
		$qb = $this->createQueryBuilder('p')
			->addSelect('pr')
			->addSelect('prv')
			->addSelect('qt')
			
			->innerJoin('p.product', 'pr')
			->innerJoin('pr.productVegetable', 'prv')
			->innerJoin('pr.quantityType', 'qt')
			
			->where('p.id = :id')
			->setParameter('id', $id)
		;
		
		$r = $qb->getQuery()
	    	->getOneOrNullResult()
		;
		
		return $r;
	}
}
