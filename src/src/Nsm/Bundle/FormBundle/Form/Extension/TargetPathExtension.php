<?php

namespace Nsm\Bundle\FormBundle\Form\Extension;

use Nsm\Bundle\FormBundle\Form\EventListener\EnsureTargetPathFieldListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TargetPathExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['target_path_choices'])) {
            return;
        }

        $listener = new EnsureTargetPathFieldListener(
            $builder->getFormFactory(),
            $options['target_path_name'],
            $options['target_path_choices']
        );

        $builder
            ->setAttribute('target_path_name', $options['target_path_name'])
            ->setAttribute('target_path_choices', $options['target_path_choices'])
            ->addEventListener(FormEvents::PRE_SET_DATA, array($listener, 'ensureTargetPathField'), -10)
            ->addEventListener(FormEvents::PRE_SUBMIT, array($listener, 'ensureTargetPathField'), -10);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'target_path_enabled' => true,
                'target_path_choices' => array(),
                'target_path_name' => '_target_path'
            )
        );
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
