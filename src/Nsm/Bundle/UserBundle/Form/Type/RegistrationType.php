<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Nsm\Bundle\FormBundle\DataTransformer\DateTimeZoneToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends BaseRegistrationFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add(
                $builder->create(
                    'timeZone',
                    'timezone',
                    array(
                        'data' => new \DateTimeZone('Australia/Sydney')
                    )
                )->addModelTransformer(
                        new DateTimeZoneToStringTransformer(),
                        true
                    )
            )->add(
                'locale',
                'locale',
                array(
                    'data' => 'en_AU'
                )
            )->add(
                'invitation',
                'invitation_code',
                array(
                    'mapped' => false
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_registration_form';
    }

}
