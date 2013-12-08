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
		// $collection
			// ->remove('delete')
		// ;
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
		if($object->getIsSent() && !$object->getSentAt() && 1===preg_match("/^[^@]*@[^@]*\.[^@]*$/", $object->getSentTo())) 
		{
			$message = \Swift_Message::newInstance()
				->setSubject('Amap Panier du '.$object->getDeliveredAt()->format('d/m/Y'))
				->setFrom('noreply@arthurhall.fr')
				->setTo($object->getSentTo()) // 'test.ter@arthurhall.fr'
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
		else if ($object->getSentAt() instanceof \DateTime) {
			$msg = 'Cette Livraison à déjà été envoyé par mail le '.$object->getSentAt()->format('d/m/Y').' et ne sera donc pas renvoyée.';
			$this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_warning', $msg);
		}
		else if (!$object->getSentAt() && 1!==preg_match("/^[^@]*@[^@]*\.[^@]*$/", $object->getSentTo())) {
            $msg = 'Vous devez renseigner une adresse mail de groupe valide dans le champ "Adresse mail des membres", Le mail n\'a pas été envoyé !';
            $this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_error', $msg);
            $object->setIsSent(false);
        }
        else {
            $msg = 'Vous pourrez envoyé le détail de cette livraison par mail plus tard.';
            $this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_warning', $msg);
        }
	}

	
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        	->add('deliveredAt', 'date', array('label' => 'Livré le'))
			
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
				'label' => ' : Envoyé par mail aux membres',
				'required' => false,
			))
            ->add('sentTo', null, array(
                'label' => 'Adresse mail des membres',
                'required' => false,
                'attr' => array(
                    'class' => 'well',
                )
            ))
			->add('withEggs', null, array(
                'label' => ' : Avec Oeufs',
                'required' => false,
            ))
            
			->setHelps(array(
                'panierAddon' => 'Pour sélectionner plusieurs paniers maintenez Ctrl puis clickez.',
                'panierMaxi' => 'Vous pouvez réutiliser un panier existant. Le libellé des grands et petits paniers est construit de la manière suivante : [identifiant unique] - [date de création du panier] - [prix exacte non remisé]',
                'panierMini' => 'Vous pouvez réutiliser un panier existant.',
                'image' => 'Un cadre bleu apparait autour des images sélectionnées',
                'sentTo' => 'Type : ...@yahooGroupes.com',
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
            ->add('withEggs', null, array('label' => 'Avec Oeufs'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('deliveredAt', null, array('label' => 'Livraison du'))
            ->add('withEggs', null, array('label' => 'Avec Oeufs', 'editable' => true))
            ->add('panierMaxi', null, array('label' => 'Grand Panier'))
            ->add('panierMini', null, array('label' => 'Petit Panier'))
            ->add('panierAddon', null, array('label' => 'Panier Exceptionnel'))
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