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
        $builder->add(
            'task',
            'entity',
            array(
                'class' => 'NsmApiBundle:Task',
                'empty_data' => null,
                'empty_value' => ''
            )
        );

        $builder->add(
            'title',
            'text',
            array(
                'description' => 'The task title',
            )
        );

        $builder->add(
            'description',
            'textarea',
            array(
                'required' => false
            )
        );
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
