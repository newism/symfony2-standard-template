<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Address Form Type
 */
class AddressType extends AbstractType
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
            'streetAddress',
            'text',
            array(
                'label' => 'streetAddress',
                'required' => false,
            )
        );

        $builder->add(
            'locality',
            'text',
            array(
                'label' => 'locality',
                'required' => false,
            )
        );

        $builder->add(
            'region',
            'text',
            array(
                'label' => 'region',
                'required' => false,
            )
        );

        $builder->add(
            'postalCode',
            'text',
            array(
                'label' => 'postalCode',
                'required' => false,
            )
        );

        $builder->add(
            'countryName',
            'text',
            array(
                'label' => 'countryName',
                'required' => false,
            )
        );

        $builder->add(
            'geolat',
            'text',
            array(
                'label' => 'geolat',
                'required' => false,
            )
        );

        $builder->add(
            'geolong',
            'text',
            array(
                'label' => 'geolong',
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
                'data_class' => 'Nsm\Bundle\ContactCardBundle\Entity\Address',
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
        return 'address';
    }
}
