<?php

namespace Amap\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'Titre'))
            ->add('category', null, array('label' => 'Catégorie'))
            ->add('isPublished', 'checkbox', array(
                'required' => false,
                'label' => 'Publié ?', 
                'label_attr' => array('class' => 'control-group', 'id' => 'amap_blogbundle_articletype_label_isPublished')
            )) // , 'label_attr' => array('class' => 'control-group', 'id' => 'amap_blogbundle_articletype_label_isPublished')
            ->add('content', 'textarea', array(
            	'label' => 'Contenu',
                'attr' => array(
                    'required' => false,
                    'class' => 'tinymce',
                    'data-theme' => 'advanced')
                )
            )
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
