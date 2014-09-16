<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            'text',
            array(
                'required' => false
            )
        );
        $builder->add(
            'title',
            'text',
            array(
                'required' => false
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'label' => 'Search Projects',
                'layout' => 'table',
                'data_class' => null,
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
