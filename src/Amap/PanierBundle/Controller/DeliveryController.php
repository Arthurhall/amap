<?php

namespace Amap\PanierBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Amap\PanierBundle\Entity\Delivery;

/**
 * Delivery controller.
 *
 */
class DeliveryController extends Controller
{

    /**
     * Lists all Delivery entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmapMainBundle:Delivery')->findAllWithJoin();

        return $this->render('AmapPanierBundle:Delivery:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Delivery entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Delivery')->findWithJoin($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
		
		$charts = array();
		
		$charts['panier_maxi']['str'] = $this->constructArray( $entity->getPanierMaxi()->getProduct() );
		$charts['panier_mini']['str'] = $this->constructArray( $entity->getPanierMini()->getProduct() );
		
        return $this->render('AmapPanierBundle:Delivery:show.html.twig', array(
            'entity'      => $entity,
            'charts'	=> $charts,
        ));
    }
	
	public function constructArray($products)
	{
		$array = array();
		foreach ($products as $key => $product) 
		{
			$quantity = $this->parseQuantity( $product );
			
			if ($quantity) 
			{
				$array[$key] = "
					[ '". str_replace("'", "\'", $product->getProductVegetable()) ." - $quantity g',     $quantity] 
				";
			}
		}
		
		return implode(',', $array);
	}
	
	public function parseQuantity( $product )
	{
		switch ( strtolower($product->getQuantityType()->getTitle()) ) 
		{
			case 'mg':
				return $product->getQuantity() / 1000;
				break;
			case 'g':
				return $product->getQuantity();
				break;
			case 'kg':
				return $product->getQuantity() * 1000;
				break;
			default:
				return false;
				break;
		}
	}
}
