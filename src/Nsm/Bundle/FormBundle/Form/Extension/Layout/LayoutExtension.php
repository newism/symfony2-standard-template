<?php

namespace Nsm\Bundle\FormBundle\Form\Extension\Layout;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LayoutExtension extends AbstractTypeExtension
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('control_group_attr', $options['control_group_attr']);
        $builder->setAttribute('control_group_label_attr', $options['control_group_label_attr']);
        $builder->setAttribute('control_attr', $options['control_attr']);
        $builder->setAttribute('control_label_attr', $options['control_label_attr']);
        $builder->setAttribute('control_input_attr', $options['control_label_attr']);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $isRoot         = false;
        $isControlGroup = false;
        $isControl      = false;
        $hasSiblings    = false;

        switch (true) {
            case ($form->isRoot()) :
                $isRoot = true;
                break;
            case (null === $form->getParent()->getParent()) :
                $isControlGroup = true;
                break;
            default :
                $isControl   = true;
                $hasSiblings = (count($form->getParent()->all()) > 1);
                break;
        }

        $formType = $form->getConfig()->getType()->getName();

        $controlGroupClass                      = (isset($options['control_group_attr']['class'])) ? $options['control_group_attr']['class'] : '';
        $options['control_group_attr']['class'] = trim($controlGroupClass .= " ControlGroup");
        $view->vars['control_group_attr']       = $options['control_group_attr'];

        $controlGroupLabelClass                       = (isset($options['control_group_label_attr']['class'])) ? $options['control_group_label_attr']['class'] : '';
        $options['control_group_label_attr']['class'] = trim($controlGroupLabelClass .= " ControlGroup-label");
        $view->vars['control_group_label_attr']       = $options['control_group_label_attr'];

        $controlClass = (isset($options['control_attr']['class'])) ? $options['control_attr']['class'] : '';
        $controlClass .= " Control";
        if ($hasSiblings) {
            $controlClass .= " Control--hasSiblings";
        }
        $options['control_attr']['class'] = trim($controlClass);
        $view->vars['control_attr']       = $options['control_attr'];

        $controlLabelClass                      = (isset($options['control_label_attr']['class'])) ? $options['control_label_attr']['class'] : '';
        $options['control_label_attr']['class'] = trim($controlLabelClass .= " Control-label");
        $view->vars['control_label_attr']       = $options['control_label_attr'];

        $controlInputClass                      = (isset($options['control_input_attr']['class'])) ? $options['control_input_attr']['class'] : '';
        $options['control_input_attr']['class'] = trim(
            $controlInputClass .= " Control-input Control-input--" . $formType
        );
        $options['control_input_attr']['class'] = $controlInputClass;

        $view->vars['attr'] = array_merge(
            $view->vars['attr'],
            $options['attr'],
            $options['control_input_attr']
        );

        $view->vars['isRoot']         = $isRoot;
        $view->vars['isControlGroup'] = $isControlGroup;
        $view->vars['isControl']      = $isControl;
        $view->vars['hasSiblings']    = $hasSiblings;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'control_group_attr'       => array(),
                'control_group_label_attr' => array(),
                'control_attr'             => array(),
                'control_label_attr'       => array(),
                'control_input_attr'       => array()
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
