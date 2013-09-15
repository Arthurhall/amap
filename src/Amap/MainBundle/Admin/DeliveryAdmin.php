<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class DeliveryAdmin extends Admin
{
	// setup the default sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'deliveredAt'
    );
	
	protected function configureRoutes(RouteCollection $collection)
    {
		$collection
			->remove('delete')
		;
    }
	
	/*
	 * Calls
	 */
	public function setMailer($mailer) {
        $this->mailer = $mailer;
    }
	
	public function setEntityManager($em) {
        $this->em = $em;
    }
	
	public function setTemplating($tpl) {
        $this->templating = $tpl;
    }
	
	
	public function postPersist($object)
	{
		$this->sendMail($object);
	}
	
	public function postUpdate($object)
	{
		$this->sendMail($object);
	}
	
	public function sendMail($object)
	{
		//$members = $this->em->getRepository('AmapUserBundle:User')->findMembers();
		if($object->getIsSent() && !$object->getSentAt()) 
		{
			$message = \Swift_Message::newInstance()
				->setSubject('Amap Panier du '.$object->getDeliveredAt()->format('d/m/Y'))
				->setFrom('noreply@arthurhall.fr')
				->setTo('test.ter@arthurhall.fr')
				->setCharset('UTF-8')    
				->setContentType('text/html')
				->setBody($this->templating->render('AmapPanierBundle:Delivery:email.html.twig', array('delivery' => $object)))
			;
			$this->mailer->send($message);
			
			$object->setSentAt( new \DateTime('now') );
			$this->em->flush();
			
			$msg = 'Le mail a bien été envoyé';
			$this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_success', $msg);
		}
		else {
			$msg = 'Cette Livraison à déjà été envoyé par mail le '.$object->getSentAt()->format('d/m/Y').' et ne sera donc pas renvoyée.';
			$this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_warning', $msg);
		}
	}
	
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        	->add('deliveredAt', null, array('label' => 'Livré le'))
			
            ->add('panierMaxi', 'sonata_type_model', array(
            	'label' => 'Grand Panier',
                'required' => false, 
                'expanded' => false, 
                'by_reference' => true, 
                'multiple' => false, 
                'compound' => false
            ))
            ->add('panierMini', 'sonata_type_model', array(
            	'label' => 'Petit Panier',
                'required' => false, 
                'expanded' => false, 
                'by_reference' => true, 
                'multiple' => false, 
                'compound' => false
            ))
			
			->add('panierAddon', 'sonata_type_model', array(
				'label' => 'Panier(s) Exceptionnel(s)',
                'required' => false, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => true, 
                'compound' => false
            ))
			->add('message', 'textarea', array(
        		'attr' => array(
        			'required' => false,
        			'class' => 'tinymce',
            		'data-theme' => 'advanced'
				)
			))
			->add('image', 'sonata_type_model', array(
                'required' => false, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => true, 
                'compound' => false
            ))
			->add('isSent', null, array(
				'label' => 'Envoyé par mail aux membres',
				'required' => false,
			))
			
			->setHelps(array(
                'panierAddon' => 'Pour sélectionner plusieurs paniers maintenez Ctrl puis clickez.',
                'panierMaxi' => 'Vous pouvez réutiliser un panier existant. Le libellé des grands et petits paniers est construit de la manière suivante : [identifiant unique] - [date de création du panier] - [prix exacte non remisé]',
                'panierMini' => 'Vous pouvez réutiliser un panier existant.',
                'image' => 'Un cadre bleu apparait autour des images sélectionnées',
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('panierMaxi', null, array('label' => 'Grand Panier'))
            ->add('panierMini', null, array('label' => 'Petit Panier'))
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Créé le'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Modifié le'))
            ->add('deliveredAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Livré le'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('deliveredAt', null, array('label' => 'Livraison du'))
            ->add('panierMaxi', null, array('label' => 'Grand Panier'))
            ->add('panierMini', null, array('label' => 'Petit Panier'))
			->add('createdAt', null, array('label' => 'Créé le'))
            ->add('updatedAt', null, array('label' => 'Modifié le'))
            
			->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'delete' => array(),
                    //'edit' => array(),
                )
            ))
        ;
    }
	
	protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
			->add('message')
            ->add('panierMaxi')
            ->add('panierMini')
			->add('panierAddon')
            
            ->with('Dates')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('deliveredAt')
            ->end()
       	;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        // $errorElement
			// ->with('content')
                // ->assertMaxLength(array('limit' => 500))
            // ->end()
        // ;
    }
}