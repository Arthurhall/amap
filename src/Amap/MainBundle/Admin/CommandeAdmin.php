<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class CommandeAdmin extends Admin
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
	
	public function getExportFields()
	{
	    return array('id', 'delivery', 'panierAddon', 'user', 'user.firstName', 'user.lastName', 'price', 'productDetailToStr', 'createdAt', 'updatedAt');
	}
	
	
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        	->add('delivery', null, array('label' => 'Livraison'))
            ->add('panierAddon', null, array('label' => 'Panier Exceptionnel'))
			->add('user', null, array('label' => 'Utilisateur'))
			->add('price', null, array('label' => 'Prix'))
			->add('product', 'sonata_type_model', array(
				'label' => 'Produit(s) Commandé(s)',
                'required' => false, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => true, 
                'compound' => false
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('delivery', null, array('label' => 'Livraison'))
        	->add('panierAddon', null, array('label' => 'Panier Exceptionnel'))
			->add('user', null, array('label' => 'Utilisateur'))
			->add('price', null, array('label' => 'Prix'))
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Créé le'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Modifié le'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
			->add('delivery', null, array('label' => 'Livraison'))
			->add('panierAddon', null, array('label' => 'Panier Exceptionnel'))
			->add('user', null, array('label' => 'Utilisateur'))
			->add('price', null, array('label' => 'Prix'))
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
			->add('delivery', null, array('label' => 'Livraison'))
			->add('panierAddon', null, array('label' => 'Panier Exceptionnel'))
			->add('user', null, array('label' => 'Utilisateur'))
			->add('price', null, array('label' => 'Prix'))
			->add('product', null, array('label' => 'Produit(s)'))
			->add('productDetail', 'product_detail', array('label' => 'Produit(s) avec quantité'))
            ->with('Dates')
                ->add('createdAt', null, array('label' => 'Créé le'))
    			->add('updatedAt', null, array('label' => 'Modifié le'))
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