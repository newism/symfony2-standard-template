<?php

namespace Nsm\Bundle\HelpBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipFilterType extends AbstractType
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
        $builder->add(
            'route',
            'route_choice',
            array(
                'required' => false
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
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
