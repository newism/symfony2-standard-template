<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Contact Card Form Type
 */
class ContactCardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'addresses',
            'collection',
            array(
                'label' => 'Addresses',
                'type' => new AddressType(),
                'error_bubbling' => false,
                'required' => false,
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
            )
        );
        $builder->add(
            'emails',
            'collection',
            array(
                'label' => 'Emails',
                'type' => new EmailType(),
                'error_bubbling' => false,
                'required' => false,
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
            )
        );
        $builder->add(
            'immps',
            'collection',
            array(
                'label' => 'Immps',
                'type' => new ImmpType(),
                'error_bubbling' => false,
                'required' => false,
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
            )
        );
        $builder->add(
            'telephones',
            'collection',
            array(
                'label' => 'Telephones',
                'type' => new TelephoneType(),
                'error_bubbling' => false,
                'required' => false,
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
            )
        );
        $builder->add(
            'urls',
            'collection',
            array(
                'label' => 'Urls',
                'type' => new UrlType(),
                'error_bubbling' => false,
                'required' => false,
                'by_reference' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => true,
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
                'data_class' => 'Nsm\Bundle\ContactCardBundle\Entity\ContactCard',
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
