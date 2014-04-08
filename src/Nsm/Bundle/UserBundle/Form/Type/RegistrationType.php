<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Nsm\Bundle\UserBundle\Form\DataTransformer\DateTimeZoneToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

class RegistrationType extends BaseRegistrationFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('firstName', 'text');
        $builder->add('lastName', 'text');

        $builder->add(
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
        );

        $builder->add('locale', 'locale', array(
            'data' => 'en_AU'
        ));
        $builder->add('invitation', 'nsm_user_user_invitation', array(
//            'mapped' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_user_registration';
    }

}
