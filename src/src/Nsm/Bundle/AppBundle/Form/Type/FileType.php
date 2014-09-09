<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            'text',
            array()
        );

        $builder->add(
            'uploadedFile',
            'file',
            array('required' => false)
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\AppBundle\Entity\File',
//                'by_reference' => false
            )
        );
    }

    public function getName()
    {
        return 'file';
    }
}
