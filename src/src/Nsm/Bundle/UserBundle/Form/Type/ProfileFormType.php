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

        $builder->add($name);
        $builder->add($localisation);

        $builder->add('Update Account', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_user_profile_form';
    }

    public function getParent()
    {
        return 'fos_user_profile';
    }
}
