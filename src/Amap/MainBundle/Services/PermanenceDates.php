<?php

namespace Amap\MainBundle\Services;


class PermanenceDates
{
	private $dates;
	
	function __construct() 
	{
		$now = new \DateTime('now');
		$next = new \DateTime('now');
		// N : 1 (pour Lundi) à 7 (pour Dimanche) -> 2 == mardi
		$dayWeek = $now->format('N');
		//echo $dayWeek; die('dfg');
		if ($dayWeek > 2) {
			$period = 'P'. (7 - $dayWeek + 2) .'D';
			//echo "$period"; die();
		}
		elseif ($dayWeek < 2) {
			$period = 'P1D';
		}
		else {
			$period = 'P0D';
		}
		
		$interval = new \DateInterval($period);
		
		$next->add( $interval );
		//echo $next->format('Y-m-d'); die();
		
		$begin = new \DateTime( $next->format('Y-m-d') );
		$end = new \DateTime( $next->format('Y-m-d') );
		$end->add( new \DateInterval('P3M') );
		//print_r($begin); print_r($end); die();
		
		$interval = new \DateInterval('P7D'); 
		$period = new \DatePeriod($begin, $interval, $end);
		
		foreach ($period as $key => $dt) {
			$dates[$key] = $dt;
		}
		// print_r($period); die();
		
		$this->dates = $dates;
	}
	
	public function getDates()
	{
		return $this->dates;
	}
}
