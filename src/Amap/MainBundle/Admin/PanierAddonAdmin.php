<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Amap\MainBundle\Entity\Product;

class PanierAddonAdmin extends Admin
{
	// setup the default sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );
	
	protected function configureRoutes(RouteCollection $collection)
    {
		$collection
			->remove('delete')
		;
    }
	
	
    protected function configureFormFields(FormMapper $formMapper)
    {
    	$query = $this->modelManager->getEntityManager('Amap\MainBundle\Entity\Product')
			->createQuery('SELECT p FROM AmapMainBundle:Product p INNER JOIN p.productVegetable v ORDER BY v.title ASC')
		;
		
        $formMapper
        	->add('title', null, array('required' => true))
            ->add('message')
            ->add('price', null, array('required' => false, 'label' => 'Prix'))
			->add('discountedPrice', null, array('required' => false, 'label' => 'Prix Remisé'))
			// ->add('product', 'sonata_type_collection', array(
                // //Prevents the "Delete" option from being displayed
                // 'type_options' => array('delete' => false),
				// 'by_reference' => false,
				// 'required' => true,
            // ), array(
                // 'edit' => 'inline',
                // 'inline' => 'table',
                // 'sortable' => 'id',
                // 'allow_add' => true,
            // ))
			
			->add('product', 'sonata_type_model', array(
				'label' => 'Produit',
                'required' => true, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => true, 
                'compound' => false,
                'query' => $query,
            ))
			->setHelps( array(
                'product' => 'Pour sélectionner plusieurs paniers maintenez Ctrl puis clickez.',
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('title')
        	->add('message')
            ->add('price', null, array('label' => 'Prix'))
            ->add('discountedPrice', null, array('label' => 'Prix Remisé'))
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Créé le'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Modifié le'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
			->add('title')
            ->add('message')
            ->add('product', null, array('label' => 'Produit'))
			->add('createdAt', null, array('label' => 'Créé le'))
			
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
            ->add('price', null, array('label' => 'Prix'))
            ->add('discountedPrice', null, array('label' => 'Prix Remisé'))
            ->add('product', null, array('label' => 'Produit'))
            
            ->with('Dates')
                ->add('createdAt', null, array('label' => 'Créé le'))
    			->add('updatedAt', null, array('label' => 'Modifié le'))
            ->end()
       	;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
			->with('title')
                //->assertNotEqual(array('value' => $object::NEW_TITLE))
				->assertNotBlank()
            ->end()
        ;
    }
}