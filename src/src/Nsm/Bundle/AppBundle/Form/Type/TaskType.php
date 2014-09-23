<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Nsm\Bundle\AppBundle\Entity\SubTask;
use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Entity\FeatureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'order',
            'number',
            array(
                'required' => false,
                'mapped' => false,
            )
        );

        if (true === $options['display_project']) {

            $builder->add(
                'project',
                'entity',
                array(
                    'class' => 'NsmAppBundle:Project',
                    'empty_value' => '',
                    'control_attr' => array(
                        'onchange' => 'document.getElementById(\'task_Refresh\').click();'
                    )
                )
            );

        }

        $builder->add(
            'title',
            'text',
            array(
                'help' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad aliquid autem consequatur, cumque dolor dolorem eaque explicabo hic, ipsam labore maxime minima molestias nihil nobis nostrum officiis quos similique voluptatem.'
            )
        );

        $builder->add(
            'tags',
            'text'
        );

        $builder->add(
            'description',
            'textarea',
            array(
                'required' => false
            )
        );

        $task = $builder->getData();

        $subTask = new SubTask();
        $subTask->setTask($task);

        $builder->add(
            'subTasks',
            'nsm_collection',
            array(
                'required' => false,
                'allow_add' => true,
                'prototype_data' => $subTask,
                'prototype_name' => '__sub_tasks__',
                'type' => new SubTaskType(),
                'layout' => 'table',
                'options' => array(
                    'display_task' => false,
                )
            )
        );

    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\AppBundle\Entity\Task',
                'display_project' => true
            )
        );
    }

    public function getName()
    {
        return 'task';
    }
}
