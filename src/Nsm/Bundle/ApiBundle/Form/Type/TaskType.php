<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'project',
            'entity',
            array(
                'class' => 'NsmApiBundle:Project'
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
                'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Task'
            )
        );
    }

    public function getName()
    {
        return 'task';
    }
}
