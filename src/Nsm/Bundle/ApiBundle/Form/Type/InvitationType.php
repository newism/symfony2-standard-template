<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InvitationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'code',
            'text'
        );
    }

    public function getName()
    {
        return 'invitation';
    }
}
