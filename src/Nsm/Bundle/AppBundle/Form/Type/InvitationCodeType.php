<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Nsm\Bundle\AppBundle\Form\DataTransformer\InvitationToCodeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvitationCodeType extends AbstractType
{
    /**
     * @var \Nsm\Bundle\AppBundle\Form\DataTransformer\InvitationToCodeTransformer
     */
    protected $invitationTransformer;

    /**
     * @param InvitationToCodeTransformer $invitationTransformer
     */
    public function __construct(InvitationToCodeTransformer $invitationTransformer)
    {
        $this->invitationTransformer = $invitationTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->invitationTransformer, true);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'Nsm\Bundle\AppBundle\Entity\Invitation',
                'required' => true,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'invitation_code';
    }
}
