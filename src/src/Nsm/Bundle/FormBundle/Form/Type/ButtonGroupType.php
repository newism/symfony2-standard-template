<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Nsm\Bundle\FormBundle\DataTransformer\DateRangeToArrayTransformer;
use Nsm\Bundle\FormBundle\Form\Validator\DateRangeValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ButtonGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'action',
                'choice',
                $options['action_options']
            )
            ->add(
                'submit',
                'submit',
                $options['submit_options']
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'action_options' => array(
                    'choices' => array(
                        'save' => 'Save'
                    )
                ),
                'submit_options' => array(),
                'error_bubbling' => false
            )
        );
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['control_attr']['data-widget'] = 'buttonGroup';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'button_group';
    }
}
