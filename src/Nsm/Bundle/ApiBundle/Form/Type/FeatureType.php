<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Nsm\Bundle\ApiBundle\Entity\Feature;

class FeatureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Feature $feature */
        $feature = $builder->getData();

        $builder->add(
            'project',
            'entity',
            array(
                'class' => 'NsmApiBundle:Project',
                'empty_value' => ''
            )
        );

        $builder->add(
            'title',
            'text',
            array(
                'description' => 'The task list title',
            )
        );

        $builder->add(
            'description',
            'textarea',
            array(
                'required' => false
            )
        );

        $builder->add(
            'background',
            'textarea',
            array(
                'required' => false
            )
        );

        $task = new Task();
        $task->setFeature($feature);
        $task->setProject($feature->getProject());

        $builder->add(
            'tasks',
            'nsm_collection',
            array(
                'label' => 'Scenarios',
                'required' => false,
                'allow_add' => true,
                'prototype_data' => $task,
                'type' => 'task',
                'options' => array(
                    'display_project' => false,
                    'display_features' => false
                )
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Feature'
            )
        );
    }

    public function getName()
    {
        return 'feature';
    }
}
