<?php

namespace Amap\PanierBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Amap\MainBundle\Entity\Commande;
use Amap\PanierBundle\Form\CommandeType;

/**
 * Commande controller.
 *
 */
class CommandeController extends Controller
{

    /**
     * Lists all Commande entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmapMainBundle:Commande')->findBy(
        	array('user' => $this->getUser()), 
        	array('createdAt' => 'DESC')
		);
		
		if (!$entities) {
            throw $this->createNotFoundException('Vous n\'avez aucune commande.');
        }

        return $this->render('AmapPanierBundle:Commande:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Commande entity.
     *
     */
    public function createAction($delivery, $panierAddon, Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $panierAddon = $em->getRepository('AmapMainBundle:PanierAddon')->find($panierAddon);
		$delivery = $em->getRepository('AmapMainBundle:Delivery')->find($delivery);
		
		if (!$panierAddon) {
            throw $this->createNotFoundException('Unable to find Panier Addon entity.');
        }
		if (!$delivery) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
		
        $entity  = new Commande();
		$entity->setUser( $this->getUser() );
		$entity->setPanierAddon( $panierAddon );
		$entity->setDelivery( $delivery );
		
        $form = $this->createForm(new CommandeType( $panierAddon->getId() ), $entity);
        $form->bind($request);
		
		//echo get_class($form->getData()->getPanierAddon()); die();
		//print_r($request->request); 
        if ($form->isValid()) 
        {
        	$this->quantityProduct($entity, $request->request->get('quantity'));
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('commande_show', array('id' => $entity->getId())));
        }

        return $this->render('AmapPanierBundle:Commande:new.html.twig', array(
            'entity' => $entity,
            'panier_addon' => $panierAddon,
            'delivery' => $delivery,
            'form'   => $form->createView(),
        ));
    }

	public function quantityProduct(Commande $entity, array $postQuantity)
	{
		$entity->setProductDetail($postQuantity);
		$price = 0;
		
    	foreach ($entity->getProduct() as $key => $product) 
    	{
    		$id = $product->getId();
    		$q = (array_key_exists($id, $postQuantity)) ? $postQuantity[$id] : 0;
			 
			if($q <= 0) {
				$entity->removeProduct($product);
			} else {
				$price += $product->getPrice() * $q;
			}
		}
		
		$entity->setPrice( $price );
	}

    /**
     * Displays a form to create a new Commande entity.
     *
     */
    public function newAction($delivery, $panierAddon)
    {
    	$em = $this->getDoctrine()->getManager();
        $panierAddon = $em->getRepository('AmapMainBundle:PanierAddon')->findWithProducts($panierAddon);
		$delivery = $em->getRepository('AmapMainBundle:Delivery')->find($delivery);
		$user = $this->getUser();
		
		if (!$panierAddon) {
            throw $this->createNotFoundException('Unable to find Panier Addon entity.');
        }
		if (!$delivery) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
		
		// Pour éviter de créer des doublons on check la delivery et le panierAddon :
		$commande = $em->getRepository('AmapMainBundle:Commande')->findByUserWithJoin($user, array(
			'panierAddon' => $panierAddon->getId(),
			'delivery' => $delivery->getId(),
		));
		
		if($commande && $commande->getPanierAddon() == $panierAddon && $commande->getDelivery() == $delivery) {
			return $this->redirect($this->generateUrl('commande_edit', array('id' => $commande->getId())));
		}
		
        $entity = new Commande();
		$entity->setPanierAddon( $panierAddon );
		$entity->setDelivery( $delivery );
		
        $form   = $this->createForm( new CommandeType($panierAddon->getId()), $entity );
		
        return $this->render('AmapPanierBundle:Commande:new.html.twig', array(
            'entity' => $entity,
            'panier_addon' => $panierAddon,
            'delivery' => $delivery,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Commande entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Commande')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Commande entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AmapPanierBundle:Commande:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Commande entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Commande')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Commande entity.');
        }

        $editForm = $this->createForm(new CommandeType($entity->getPanierAddon()->getId()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AmapPanierBundle:Commande:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Commande entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Commande')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Commande entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CommandeType($entity->getPanierAddon()->getId()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
        	$this->quantityProduct($entity, $request->request->get('quantity'));
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('commande_edit', array('id' => $id)));
        }

        return $this->render('AmapPanierBundle:Commande:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Commande entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AmapMainBundle:Commande')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Commande entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('commande'));
    }

    /**
     * Creates a form to delete a Commande entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
