<?php

namespace Nsm\Bundle\HelpBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'title',
            'text'
        );

        $builder->add(
            'content',
            'textarea'
        );

        $builder->add(
            'route',
            'route_choice'
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\HelpBundle\Entity\Tip'
            )
        );
    }

    public function getName()
    {
        return 'help_tip';
    }
}
