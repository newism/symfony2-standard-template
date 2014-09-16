<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Form\DataTransformer\ChoiceToValueTransformer;
use Nsm\Bundle\ContactCardBundle\Form\Type\ContactCardType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormFactory;

class ProjectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = $builder->getData();

        $builder->add(
            'text',
            'text',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'textarea',
            'textarea',
            array(
                'mapped' => false
            )
        );


        $builder->add(
            'choice',
            'choice',
            array(
                'mapped' => false,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'choice-expanded-multiple',
            'choice',
            array(
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'choice-expanded',
            'choice',
            array(
                'mapped' => false,
                'expanded' => true,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'date',
            'date',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'datetime',
            'datetime',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'datetime-single',
            'datetime',
            array(
                'widget' => 'single_text',
                'mapped' => false
            )
        );

        $builder->add(
            'birthday',
            'birthday',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'startedAtRange',
            'date_range',
            array(
                'required' => true,
                'help' => 'Dates are inclusive',
                'mapped' => false
            )
        );

        $builder->add(
            'title',
            'text'
        );

        $task = new Task();
        $task->setProject($project);

        $builder->add(
            'tasks',
            'nsm_collection',
            array(
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'allow_sort' => true,
                'sort_control' => 'order',
                'type' => 'task',
                'prototype_data' => $task,
                'prototype_name' => '__tasks__',
                'layout' => 'stacked',
                'options' => array(
                    'display_project' => false
                )
            )
        );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'layout' => 'stacked',
                'data_class' => 'Nsm\Bundle\AppBundle\Entity\Project'
            )
        );
    }

    public function getName()
    {
        return 'project';
    }
}
