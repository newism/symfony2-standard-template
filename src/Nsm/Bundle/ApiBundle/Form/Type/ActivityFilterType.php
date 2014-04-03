<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Entity\TaskRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActivityFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taskFormEventHandler = function (FormEvent $event, $eventName) {

            $form = $event->getForm();
            $data = $event->getData();

            // Base options
            $options = array(
                'label'    => 'Task',
                'class'    => 'NsmApiBundle:Task',
                'multiple' => false
            );

            $tasks = isset($data['task']) ? $data['task'] : null;

            // If the tasks are null then load empty choices
            if (null === $tasks) {
                $options['choices'] = array();

                // Otherwise load a query builder
            } else {

                // Add the query builder to limit the valid options
                $options['query_builder'] = function (TaskRepository $repo) use ($tasks) {
                    $qb = $repo->createQueryBuilder('Task');
                    $qb->where($qb->expr()->in('Task.id', ':tasks'));
                    $qb->setParameter('tasks', $tasks);

                    return $qb;
                };
            }

            // Add the form type
            $form->add(
                'task',
                'entity_search',
                $options
            );
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $taskFormEventHandler);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $taskFormEventHandler);

        $builder
            ->add(
                'id',
                'text',
                array(
                    'property_path'            => '[id]',
                    'required'                 => false,
                    'constraints'              => array(
                        new NotBlank(),
                        new Length(array('min' => 3)),
                    ),
                    'help'                     => 'Test Help',
                    'control_input_attr'       => array(
                        'class' => 'xxx',
                        'foo'   => 'bar'
                    ),
                    'control_group_attr'       => array(
                        'class' => 'cg',
                        'pink'  => 'purple'
                    ),
                    'control_group_label_attr' => array(
                        'class' => 'cg',
                        'pink'  => 'purple'
                    ),
                    'control_attr'             => array(
                        'class' => 'c',
                        'pink'  => 'p'
                    ),
                    'control_label_attr'       => array(
                        'class' => 'cg',
                        'pink'  => 'purple'
                    )
                )
            )
            ->add(
                'title',
                'text',
                array(
                    'property_path' => '[title]',
                    'required'      => true
                )
            )
            ->add(
                'endedAt',
                'choice',
                array(
                    'label'      => 'Ended At',
                    'required'   => true,
                    'empty_data' => null,
                    'choices'    => array(
                        'isNotNull' => 'Ended',
                        'isNull'    => 'Not Ended'
                    )
                )
            )
            ->add(
                'startedAtRange',
                'date_range',
                array(
                    'required' => true,
                    'help'     => 'Dates are inclusive'
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'csrf_protection' => false,
                'required'        => true
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
