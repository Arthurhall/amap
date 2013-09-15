<?php

namespace Amap\PanierBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;

class CommandeType extends AbstractType
{
	private $id;
	
	public function __construct( $id = null )
	{
		$this->id = $id;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$id = $this->id;
		
        $builder
        	->add('productDetail')
			
			// ->add('price', 'hidden')
			// ->add('delivery')
        	// ->add('panierAddon', 'entity', array(
        		// 'class' => 'Amap\MainBundle\Entity\PanierAddon',
				// 'query_builder' => function(EntityRepository $er) use ($id) {
			        // return $er->createQueryBuilder('p')
						// ->where('p.id = :id')
						// ->setParameter('id', $id)
					// ;
			    // },
			// ))
            
            ->add('product', 'entity', array(
            	'class' => 'Amap\MainBundle\Entity\Product',
            	'expanded' => true,
            	'multiple' => true,
            	'required' => false,
            	'query_builder' => function(EntityRepository $er) use ($id) {
			        return $er->createQueryBuilder('p')
						->innerJoin('p.panierAddon', 'a')
						->where('a.id = :id')
						->setParameter('id', $id)
					;
			    },
			))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amap\MainBundle\Entity\Commande'
        ));
    }

    public function getName()
    {
        return 'amap_panierbundle_commandetype';
    }
}
