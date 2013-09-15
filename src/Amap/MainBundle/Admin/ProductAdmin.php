<?php 

namespace Amap\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class ProductAdmin extends Admin
{
	// setup the default sort column and order
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt'
    );
	
	protected function configureRoutes(RouteCollection $collection)
    {
		$collection
			->remove('edit')
		;
    }
	
	
	// Dédoublonnage :
	public function setEntityManager($em) {
        $this->em = $em;
    }

    public function getEntityManager() {
        return $this->em;
    }
	
	public function prePersist($object)
	{
		$products = $this->em->getRepository('AmapMainBundle:Product')->findBy(array(
			'quantityType' => $object->getQuantityType(),
			'productVegetable' => $object->getProductVegetable(),
			'quantity' => $object->getQuantity(),
			'price' => $object->getPrice(),
		));
		
		if($products)
		{
			foreach ($products as $key => $product) {
				$msg = 'Attention, le produit que vous essayez de créer existe déjà (id:'.$product->getId().') !';
	            $this->getRequest()->getSession()->getFlashBag()->add('sonata_flash_error', $msg);
			}
		}
	}
	
	public function preUpdate($object)
	{
		
	}
	
	
    protected function configureFormFields(FormMapper $formMapper)
    {
    	$query = $this->modelManager->getEntityManager('Amap\MainBundle\Entity\ProductVegetable')
			->createQuery('SELECT v FROM AmapMainBundle:ProductVegetable v ORDER BY v.title ASC')
		;
		
        $formMapper
            ->add('productVegetable', 'sonata_type_model', array(
            	'label' => 'Nature du Produit',
                'required' => true, 
                'expanded' => false, 
                'by_reference' => false, 
                'multiple' => false, 
                'compound' => false,
                'query' => $query,
            ))
            
			->add('price', 'number', array('label' => 'Prix'))
			->add('quantity', 'number', array('label' => 'Quantité'))
			->add('quantityType', 'entity', array(
				'class' => 'AmapMainBundle:QuantityType', 
				'label' => 'Unité de Quantité',
				'property' => 'title',
			))
			->add('title', 'text', array(
                'label' => 'Titre automatique', 
                'required' => false,
                'disabled' => true,
                'attr' => array('placeholder' => 'Généré automatiquement')
            ))
            ->setHelps(array(
                'title' => 'Si le produit existe déjà une erreur apparaitra ici',
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        	->add('productVegetable', null, array(), 'entity', array(
        		'label' => 'Nature du Produit',
        		'class' => 'AmapMainBundle:ProductVegetable', 
        		'expanded' => false, 
        		'multiple' => false,
        		'by_reference' => false, 
        		'compound' => false,
			))
        	//->add('productVegetable', null, array('class' => 'AmapMainBundle:QuantityType', 'label' => 'Nature du Produit'))
			->add('price', null, array('label' => 'Prix'))
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Créé le'))
			->add('updatedAt', 'doctrine_orm_date', array('input_type' => 'date', 'label' => 'Modifié le'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('productVegetable', null, array('label' => 'Nature du Produit'))
            ->add('price', null, array('label' => 'Prix'))
			->add('quantity', null, array('label' => 'Quantité'))
			->add('quantityType', null, array('label' => 'Unité de Quantité'))
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
			->add('title')
			->add('productVegetable', null, array('label' => 'Nature du Produit'))
			//->add('description')
            ->add('price', null, array('label' => 'Prix'))
			->add('quantity', null, array('label' => 'Quantité'))
			->add('quantityType', null, array('label' => 'Unité de Quantité'))
			
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
                ->assertNotBlank()
            ->end()
        ;
    }
}