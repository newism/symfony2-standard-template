<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ContactCardBundle\Form\Type\ContactCardType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Activity Form Type
 */
class ActivityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'title',
            'text',
            array(
                'label' => 'title',
                'required' => true,
            )
        );

        $builder->add(
            'description',
            'text',
            array(
                'label' => 'description',
                'required' => false,
            )
        );

        $builder->add(
            'createdAt',
            'datetime',
            array(
                'label' => 'createdAt',
                'required' => false,
            )
        );

        $builder->add(
            'updatedAt',
            'datetime',
            array(
                'label' => 'updatedAt',
                'required' => false,
            )
        );

        $builder->add(
            'deletedAt',
            'datetime',
            array(
                'label' => 'deletedAt',
                'required' => false,
            )
        );
        // Many To One
        $builder->add(
            'task',
            'entity',
            array(
                'label' => 'task',
                'class' => 'NsmApiBundle:Task',
                'property' => 'id',
                'query_builder' => function (\Nsm\Bundle\ApiBundle\Entity\TaskRepository $repo) {
                    $qb = $repo->createQueryBuilder('Task');

                    return $qb;
                }
            )
        );
        // Many To One
        $builder->add(
            'contactCard',
            new ContactCardType(),
            array(
                'label' => 'contactCard'
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Activity',
                'cascade_validation' => true,
                'error_bubbling' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'activity';
    }
}
