<?php

namespace Amap\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('alt')
			->add('vitrine')
            ->add('file', 'file', array('mapped' => false))
			->add('user', 'hidden', array('mapped' => false))
			->add('url_delete', 'hidden', array('mapped' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Amap\MainBundle\Entity\Image'
        ));
    }

    public function getName()
    {
        return 'amap_mainbundle_imagetype';
    }
}
