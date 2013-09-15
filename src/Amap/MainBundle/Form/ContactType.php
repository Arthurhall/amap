<?php

namespace Amap\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	
        $builder
        	->add('civilite', 'choice', array(
				'choices' => array(
					'Mlle' => 'Mlle',
					'Mme' => 'Mme',
					'Mr' => 'Mr',
				),
				'expanded' => true,
				'required' => false
			))
            
			->add('nom', 'text', array('attr' => array('placeholder' => 'Nom') ) )
			->add('prenom', 'text', array('attr' => array('placeholder' => 'Prénom') ) )
			->add('email', 'email', array('attr' => array('placeholder' => 'Votre e-mail') ) )
			->add('tel', 'text', array('required' => false, 'attr' => array('placeholder' => 'Téléphone') ) )

			
			->add('sujet', 'text', array('attr' => array('placeholder' => 'Sujet'), 'label' => '' ) )
			
			->add('message', 'textarea', array(
        		'attr' => array(
        			'class' => 'tinymce',
            		'data-theme' => 'simple'
				),
				'required' => false
    		))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    public function getName()
    {
        return 'amap_mainbundle_contacttype';
    }
}
