<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Nsm\Bundle\ContactCardBundle\Entity\Email;
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
            'choice',
            array(
                'label' => 'type',
                'choices' => array(
                    0 => 'choice 1',
                    1 => 'choice 2',
                )
            )
        );

        $builder->add(
            'typeCustom',
            'text',
            array(
                'label' => 'typeCustom',
                'required' => false,
            )
        );

        $builder->add(
            'value',
            'text',
            array(
                'label' => 'value',
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
        return 'contact_card_email';
    }
}
