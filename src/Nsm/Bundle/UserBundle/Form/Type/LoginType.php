<?php

namespace Nsm\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                '_username',
                'text'
            )
            ->add(
                '_password',
                'password'
            )
            ->add(
                '_remember_me',
                'checkbox',
                array(
                    'required' => false
                )
            )
            ->add(
                '_failure_path',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                '_target_path',
                'text',
                array(
                    'required' => false
                )
            )
            ->add(
                'invitation',
                'invitation',
                array(
                    'required' => false
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'intention' => 'authenticate',
                'csrf_field_name' => '_csrf_token'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        // Login forms do not have a name
        // Adding a name prefixes the submitted data which
        // results in the authenticator not matching the csrf token
        // and other data
        return null;
    }
}
