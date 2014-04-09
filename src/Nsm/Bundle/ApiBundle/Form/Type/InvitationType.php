<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Nsm\Bundle\ApiBundle\Form\DataTransformer\InvitationToCodeTransformer;

class InvitationType extends AbstractType
{
    /**
     * @var \Nsm\Bundle\ApiBundle\Form\DataTransformer\InvitationToCodeTransformer
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
        $resolver->setDefaults(array(
                'class' => 'Nsm\Bundle\UserBundle\Entity\Invitation',
                'required' => true,
            ));
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
        return 'nsm_user_user_invitation';
    }
}
