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
        $builder
            ->add('title', 'text', array(
                'description' => 'The task title',
            ))
            ->add('project', 'entity', array('class' => 'NsmApiBundle:Project'))
            ->add('Create Task', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Task'
        ));
    }

    public function getName()
    {
        return 'task';
    }
}
