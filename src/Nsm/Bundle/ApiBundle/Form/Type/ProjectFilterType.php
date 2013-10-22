<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

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
        $builder
            ->add(
                'id',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                'title',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                'createdAtRange',
                'date_range',
                array(
                    'mapped' => false,
                    'cascade_validation' => true,
//                    'required' => false,
                    'start_options' => array(
                        'required' => false
                    ),
                    'end_options' => array(
                        'required' => false
                    )
                )
            )
            ->add('search', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
