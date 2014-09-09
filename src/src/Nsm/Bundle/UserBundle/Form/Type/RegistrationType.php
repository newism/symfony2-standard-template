<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Nsm\Bundle\FormBundle\DataTransformer\DateTimeZoneToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                    'timeZone', // Property
                    'time_zone', // FormType (see Nsm\Bundle\FormBundle\Form\Type\TimeZoneType)
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
            )
            // The invitation code must be on this form to help validate that the person can register
            // We may have already checked this in the claim route but it's not secure
            ->add(
                'invitation',
                'invitation_code',
                array(
                    'mapped' => false,
                    // Enforce a invitation
                    'constraints' => array(
                        new NotBlank(),
                    ),
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
