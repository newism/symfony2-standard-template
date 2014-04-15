<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InvitationClaimType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'invitation_code',
                'invitation'
            );
    }

    public function getName()
    {
        return 'invitation_claim';
    }
}
