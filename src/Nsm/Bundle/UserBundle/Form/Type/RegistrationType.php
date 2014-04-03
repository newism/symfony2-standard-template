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
        $builder->add('timeZone', 'timeZone');
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
