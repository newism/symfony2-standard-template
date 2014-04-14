<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Nsm\Bundle\FormBundle\DataTransformer\DateTimeZoneToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends BaseProfileFormType
{
    /**
     * {@inheritdoc}
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                null,
                array(
                    'label' => 'form.username',
                    'translation_domain' => 'FOSUserBundle'
                )
            );

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

        $builder->add(
            'locale',
            'locale',
            array(
                'data' => 'en_AU'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_profile_form';
    }
}
