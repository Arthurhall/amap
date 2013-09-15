<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ArticleAdmin extends Admin
{
	// setup the default sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );
	
	protected function configureRoutes(RouteCollection $collection)
    {
		$collection
			//->remove('delete')
		;
    }
    
    public $supportsPreviewMode = true;
	
	public function setSecurityContext($securityContext) {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext() {
        return $this->securityContext;
    }
	
	public function setEntityManager($em) {
        $this->em = $em;
    }

    public function getEntityManager() {
        return $this->em;
    }
	
	public function prePersist($object)
	{
		$object->setUser( $this->getSecurityContext()->getToken()->getUser() );
		$this->checkForHome($object);
	}
	
	public function preUpdate($object)
	{
		$this->checkForHome($object);
	}
	
	public function checkForHome($article)
	{
        $this->checkForCancelHome($article);
        $this->newHome($article);
	}
    
    public function checkForCancelHome($article)
    {
        //Case it was home but not anymore :
        if(!$article->getIsHome() && $article->getIsHomeSince()) {
            $article->setIsHome(true);
            $msg = 'Attention, vous ne pouvez pas supprimer la page d\'accueiil !';
            $this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_error', $msg);
        }    
    }
    
    public function newHome($article)
    {
        //Case this is the new home :
        if($article->getIsHome() && !$article->getIsHomeSince()) {
            $article->setIsHomeSince( new \DateTime('now') );
            $home = $this->em->getRepository('AmapMainBundle:Article')->findHome();
            if($home) {
                $home->setIsHome(false);
                $home->setIsHomeSince( null );
            }
        }
    }
	
	
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
			->add('isPublished', 'checkbox', array('required' => false))
			->add('isHome', 'checkbox', array('required' => false))
			->add('category')
			->add('user')
            ->add('content', 'textarea', array(
        		'attr' => array(
        			'required' => false,
        			'class' => 'tinymce',
            		'data-theme' => 'advanced')
    			)
			)
			->add('image', 'sonata_type_model', array(
                'required' => false, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => true, 
                'compound' => false
            ))
			
			->setHelps(array(
                'image' => 'Un cadre bleu apparait autour des images sélectionnées',
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('title')
            ->add('content')
			->add('category')
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date'))
            ->add('publishedAt', 'doctrine_orm_date', array('input_type' => 'date'))
            ->add('isPublished')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
			->add('user')
            ->add('isPublished')
			->add('isHome')
            ->add('category')
			->add('createdAt')
			
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
            ->add('title')
			->add('user')
            ->add('content', 'text_raw')
			->add('isPublished')
            ->add('category')
            ->add('image', 'image')
            ->with('Dates')
                ->add('publishedAt')
                ->add('createdAt')
    			->add('updatedAt')
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