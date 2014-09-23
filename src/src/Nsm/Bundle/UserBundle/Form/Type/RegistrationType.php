<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Nsm\Bundle\FormBundle\DataTransformer\DateTimeZoneToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name = $builder->create(
            'name',
            'form',
            array(
                'inherit_data' => true
            )
        )
            ->add('firstname', 'text')
            ->add('lastName', 'text');

        $localisation = $builder->create(
            'localisation',
            'form',
            array(
                'inherit_data' => true
            )
        );

        $timezone = $builder->create(
            'timeZone', // Property
            'time_zone', // FormType (see Nsm\Bundle\FormBundle\Form\Type\TimeZoneType)
            array(
                'data' => new \DateTimeZone('Australia/Sydney')
            )
        )->addModelTransformer(
            new DateTimeZoneToStringTransformer(),
            true
        );

        $locale = $builder->create(
            'locale',
            'locale',
            array(
                'data' => 'en_AU'
            )
        );

        $localisation->add($locale);
        $localisation->add($timezone);

        $builder
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'label' => 'Password',
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                )
            )
            ->add($name)
            ->add($localisation)
            // The invitation code must be on this form to help validate that the person can register
            // We may have already checked this in the claim route but it's not secure
            ->add(
                'invitation',
                'invitation_code',
                array(
                    'label' => 'Invitation Code',
                    'mapped' => false,
                    // Enforce a invitation
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add('Create Account', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_registration_form';
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

}
