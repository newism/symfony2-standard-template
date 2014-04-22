<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Email Form Type
 */
class EmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'type',
            'text',
            array(
                'label' => 'type',
                'required' => false,
            )
        );

        $builder->add(
            'value',
            'text',
            array(
                'label' => 'value',
                'required' => false,
            )
        );

        $builder->add(
            'preferred',
            'checkbox',
            array(
                'label' => 'preferred',
                'required' => false,
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
                'data_class' => 'Nsm\Bundle\ContactCardBundle\Entity\Email',
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
        return 'email';
    }
}
