<?php

namespace Amap\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category')
            ->add('isPublished')
            ->add('content', 'textarea', array(
                'attr' => array(
                    'required' => false,
                    'class' => 'tinymce',
                    'data-theme' => 'advanced')
                )
            )
            
            ->add('delivery')
            ->add('image')
            
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amap\MainBundle\Entity\Article'
        ));
    }

    public function getName()
    {
        return 'amap_blogbundle_articletype';
    }
}
