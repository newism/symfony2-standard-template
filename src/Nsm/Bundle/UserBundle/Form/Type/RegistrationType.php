<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder->add('timeZone', 'timezone', array(
            'data' => 'Australia/Sydney'
        ));
        $builder->add('locale', 'locale', array(
            'data' => 'en_AU'
        ));
        $builder->add('invitation', 'nsm_user_user_invitation');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_user_registration';
    }

}
