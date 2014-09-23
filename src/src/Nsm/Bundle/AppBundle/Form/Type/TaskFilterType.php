<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Nsm\Bundle\AppBundle\Entity\ProjectRepository;
use Nsm\Bundle\AppBundle\Entity\TaskRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add(
            'id',
            'text',
            array(
                'required' => false
            )
        );

        $builder->add(
            'title',
            'text',
            array(
                'required' => false
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'label' => 'Search Tasks',
                'layout' => 'table',
                'data_class' => null,
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
