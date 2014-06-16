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
//        $taskForm = $builder->create(
//            'task',
//            'form',
//            array(
//                'mapped' => false,
//                'data_class' => 'Nsm\Bundle\AppBundle\Entity\Task'
//            )
//        );
//        $taskForm->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'taskEventHandler'));
//        $taskForm->addEventListener(FormEvents::SUBMIT, array($this, 'taskEventHandler'));
//
//        $builder->add($taskForm);

        $builder->add(
            'title',
            'text'
        );


        $builder->add(
            'contactCard',
            new ContactCardType()
        );

        $project = $builder->getData();

        $task = new Task();
        $task->setProject($project);

        $builder->add(
            'tasks',
            'nsm_collection',
            array(
                'required' => false,
                'allow_add' => true,
                'prototype_data' => $task,
                'prototype_name' => '__tasks__',
                'type' => 'task',
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
                'data_class' => 'Nsm\Bundle\AppBundle\Entity\Project'
            )
        );
    }

    public function getName()
    {
        return 'project';
    }

    public function taskEventHandler(FormEvent $event, $eventName)
    {
        /** @var Task $task */
        $task = $event->getData();

        /** @var Form $form */
        $form = $event->getForm();

        if (FormEvents::PRE_SET_DATA === $eventName) {

            $form->add(
                'toggle',
                'choice',
                array(
                    'label' => null,
                    'mapped' => false,
                    'choices' => array(
                        'existing' => 'Select a Task',
                        'new' => 'Create a new Task',
                    )
                )
            );

            $form->add(
                'existing',
                'entity',
                array(
                    'label' => 'Select a Task',
                    'class' => 'NsmAppBundle:Task',
                    'mapped' => false,
                )
            );

            $form->add(
                'new',
                'task',
                array(
                    'label' => 'Create a new Task',
                    'display_project' => false,
                    'mapped' => false,
                )
            );
        }

        if (FormEvents::SUBMIT === $eventName) {
            $newOrExisting = $form->get('toggle')->getNormData();
            $normData = $form->get($newOrExisting)->getNormData();
            $event->setData($normData);
        }
    }

}
