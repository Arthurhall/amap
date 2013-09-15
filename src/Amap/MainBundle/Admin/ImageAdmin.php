<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ImageAdmin extends Admin
{
	public function prePersist($object) {
    	$this->saveFile($object);
	}

  	public function preUpdate($object) {
    	$this->saveFile($object);
  	}
 
  	public function saveFile($object) {
    	$basepath = $this->getRequest()->getBasePath();
    	$object->upload($basepath); 
  	}
	
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
	
	
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('alt')
			->add('file', 'file', array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('id')
        	->add('title')
            ->add('alt')
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
        	->addIdentifier('id')
            ->addIdentifier('title')
            ->add('alt')
            ->add('path')
            ->add('article')
            ->add('webPath', 'image')
			->add('createdAt')
			->add('updatedAt')
            
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
          	->add('title')
            ->add('alt')
			->add('webPath', 'image')
            ->with('Dates')
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