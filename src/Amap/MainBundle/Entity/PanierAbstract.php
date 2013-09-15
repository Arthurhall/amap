<?php

namespace Amap\MainBundle\Entity;

abstract class PanierAbstract
{
    // Force les classes filles à définir cette méthode
    abstract public function getId();
    abstract public function getCreatedAt();
    abstract public function getPrice();
    abstract public function getDiscountedPrice();
    
	public function __toString() 
	{
		if($this->getCreatedAt() instanceof \DateTime) {
			return (string) $this->getId().' - '.$this->getCreatedAt()->format('d/m/Y').' - '.$this->getPriceFormat().$this->getDevise();
		}
		return (string) 'Nouveau Panier';
	}
	
	public function getPriceFormat()
	{
		return number_format($this->getPrice(), 2, ',', ' ');
	}
	
	public function getDiscountedPriceFormat()
	{
		return number_format($this->getDiscountedPrice(), 2, ',', ' ');
	}
	
	public function getDevise()
	{
		return '€';
	}
    
}