<?php

namespace Amap\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PermanenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        	->add('deliveryDate', 'date', array(
        		'label' => 'Date',
			))
            ->add('userDetail', 'hidden')
			->add('heure', 'hidden', array(
				'mapped' => false,
			))
            ->add('users')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amap\MainBundle\Entity\Permanence'
        ));
    }

    public function getName()
    {
        return 'amap_mainbundle_permanencetype';
    }
}
