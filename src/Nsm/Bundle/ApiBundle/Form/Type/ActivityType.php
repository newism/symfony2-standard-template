<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\FormBundle\Form\Type\DateRangeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                'text',
                array(
                    'description' => 'The task title',
                )
            )
            ->add(
                'dateRange',
                new DateRangeType(),
                array(
                    'required'   => false,
                    'start_type' => 'datetime',
                    'end_type'   => 'datetime',
                )
            )
            ->add(
                'task',
                'entity',
                array(
                    'class' => 'NsmApiBundle:Task'
                )
            )
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Activity'
            )
        );
    }

    public function getName()
    {
        return 'activity';
    }
}
