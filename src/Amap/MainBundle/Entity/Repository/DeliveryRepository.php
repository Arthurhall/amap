<?php

namespace Amap\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Tools\Pagination\Paginator;


class DeliveryRepository extends EntityRepository
{
    public function findNextPanierAddons()
    {
        $qb = $this->createQueryBuilder('d')
            ->addSelect('padd')
            ->addSelect('padd_prod')
            ->orderBy('d.deliveredAt', 'ASC')
            
            ->innerJoin('d.panierAddon', 'padd')
            ->leftJoin('padd.product', 'padd_prod')
            
            ->where('d.deliveredAt > :now')
            ->setParameter('now', $this->getNow())
        ;
        
        $r = $qb->getQuery()
            ->getResult()
        ;
        
        return $r;
    }
    
	public function findNextOneWithJoin()
	{
		$qb = $this->createQueryBuilder('d')
			->addSelect('pmax')
			->addSelect('pmax_prod')
			// ->addSelect('pmax_prod_vege')
			// ->addSelect('pmax_prod_qtype')
			
			->addSelect('pmin')
			->addSelect('pmin_prod')
			// ->addSelect('pmin_prod_vege')
			// ->addSelect('pmin_prod_qtype')
			
			->addSelect('padd')
			->addSelect('padd_prod')
			// ->addSelect('padd_prod_vege')
			// ->addSelect('padd_prod_qtype')
			
			->innerJoin('d.panierMaxi', 'pmax')
			->innerJoin('pmax.product', 'pmax_prod')
			// ->innerJoin('pmax_prod.productVegetable', 'pmax_prod_vege')
			// ->innerJoin('pmax_prod.quantityType', 'pmax_prod_qtype')
			
			->innerJoin('d.panierMini', 'pmin')
			->innerJoin('pmin.product', 'pmin_prod')
			// ->innerJoin('pmin_prod.productVegetable', 'pmin_prod_vege')
			// ->innerJoin('pmin_prod.quantityType', 'pmin_prod_qtype')
			
			->leftJoin('d.panierAddon', 'padd')
			->leftJoin('padd.product', 'padd_prod')
			// ->leftJoin('padd_prod.productVegetable', 'padd_prod_vege')
			// ->leftJoin('padd_prod.quantityType', 'padd_prod_qtype')
			
			->where('d.deliveredAt > :now')
			->setParameter('now', $this->getNow())
			
			->orderBy('d.deliveredAt', 'ASC')
			->setMaxResults(1)
		;
		
		
		$paginator = new Paginator($qb->getQuery(), $fetchJoinCollection = true);
		$delivery = null;
        foreach ($paginator as $key => $d) {
			$delivery = $d;
		}
        
		return $delivery;
	}
	
	public function findWithJoin($id)
	{
		$qb = $this->createQueryBuilder('d')
			->addSelect('pmax')
			->addSelect('pmax_prod')
			->addSelect('pmax_prod_vege')
			->addSelect('pmax_prod_qtype')
			
			->addSelect('pmin')
			->addSelect('pmin_prod')
			->addSelect('pmin_prod_vege')
			->addSelect('pmin_prod_qtype')
			
			->innerJoin('d.panierMaxi', 'pmax')
			->innerJoin('pmax.product', 'pmax_prod')
			->innerJoin('pmax_prod.productVegetable', 'pmax_prod_vege')
			->innerJoin('pmax_prod.quantityType', 'pmax_prod_qtype')
			
			->innerJoin('d.panierMini', 'pmin')
			->innerJoin('pmin.product', 'pmin_prod')
			->innerJoin('pmin_prod.productVegetable', 'pmin_prod_vege')
			->innerJoin('pmin_prod.quantityType', 'pmin_prod_qtype')
			
			// ->where('d.deliveredAt < :now')
			// ->setParameter('now', $this->getNow())
			
			->where('d.id = :id')
			->setParameter('id', $id)
		;
		
		$r = $qb->getQuery()
	    	->getOneOrNullResult()
		;
		
		return $r;
	}
	
	public function findAllWithJoin()
	{
		$qb = $this->createQueryBuilder('d')
			->addSelect('pmax')
			->addSelect('prod')
			->addSelect('vege')
			
			->innerJoin('d.panierMaxi', 'pmax')
			->innerJoin('pmax.product', 'prod')
			->innerJoin('prod.productVegetable', 'vege')
			
			->where('d.deliveredAt < :now')
			->setParameter('now', $this->getNow())
			->orderBy('d.deliveredAt', 'DESC')
		;
		
		$r = $qb->getQuery()
	    	->getResult()
		;
		
		return $r;
	}
	
	public function getNow()
	{
		$DateTimeNow = new \DateTime('now');
        $now = $DateTimeNow->format('Y-m-d h:i:s');
		return $now;
	}
	
	public function findAllByMonth( array $arr = null )
    {
        $now = $this->getNow();
        
    	$sql = "
    		SELECT d.*,
    		DATE_FORMAT(d.delivered_at,'%Y-%m') as formatted_date
    		FROM delivery as d
    		WHERE d.delivered_at < '$now'  
    		ORDER BY d.delivered_at DESC
    		";
    	
		$conn = $this->getEntityManager()->getConnection();
		$r = array();
		foreach($conn->query($sql)->fetchAll() as $k => $row)
		{
			$r[$k]       = $row;
			$dates[$k]   = $row['formatted_date'];
		}
		
		if(!$r) return $r;
		
		$uniq_dates = array_unique($dates);
		
		foreach($uniq_dates as $k => $d)
		{
			foreach ($r as $key => $row) 
			{
			    if($row['formatted_date'] == $d) {
				    $res[$d][$row['id']] = $row;
                }
			}
		}
		//echo "<pre>"; print_r($r) ; die();
		return $res;
   	}
	
}
