<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Entity\FeatureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubTaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (true === $options['display_task']) {

            $builder->add(
                'task',
                'entity',
                array(
                    'class' => 'NsmAppBundle:Task',
                    'empty_value' => '',
                    'control_attr' => array(
                        'onchange' => 'document.getElementById(\'task_Refresh\').click();'
                    )
                )
            );
        }

        $builder->add(
            'title',
            'text'
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

        $builder->add(
            'order',
            'number',
            array(
                'required' => false,
                'mapped' => false
            )
        );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\AppBundle\Entity\SubTask',
                'display_task' => true,
            )
        );
    }

    public function getName()
    {
        return 'sub_task';
    }
}
