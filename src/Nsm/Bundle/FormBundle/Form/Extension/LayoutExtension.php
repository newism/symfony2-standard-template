<?php

namespace Nsm\Bundle\FormBundle\Form\Extension;

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

        $builder->setAttribute('collection_attr', $options['collection_attr']);
        $builder->setAttribute('collection_label_attr', $options['collection_label_attr']);
        $builder->setAttribute('collection_item_attr', $options['collection_item_attr']);
        $builder->setAttribute('collection_item_label_attr', $options['collection_item_label_attr']);

        $builder->setAttribute('control_attr', $options['control_attr']);
        $builder->setAttribute('control_label_attr', $options['control_label_attr']);
        $builder->setAttribute('control_input_attr', $options['control_label_attr']);
    }


    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function isFormPrototype(FormInterface $form)
    {
        return ("label__" === substr($form->getConfig()->getOption('label'), -7));
    }


    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $isRoot = false;
        $isControlGroup = false;
        $isCollection = false;
        $isCollectionItem = false;
        $isControl = false;
        $hasSiblings = false;

        $formType = $form->getConfig()->getType()->getName();
        $formTypeIsPrototype = $this->isFormPrototype($form);

        $formParent = $form->getParent();
        $formParentType = null;
        $formParentTypeIsPrototype = false;

        if($formParent) {
            $formParentType = $formParent->getConfig()->getType()->getName();
            $formParentTypeIsPrototype = $this->isFormPrototype($formParent);
        }

        switch (true) {
            case ("nsm_collection" === $formType || "collection" === $formType) :
                $isCollection = true;
                break;
            case ($formTypeIsPrototype || "nsm_collection" === $formParentType || "collection" === $formParentType) :
                $isCollectionItem = true;
                break;
            case ($form->isRoot()) :
                $isRoot = true;
                break;
            case (!$formParentTypeIsPrototype && true === $formParent->isRoot()) :
                $isControlGroup = true;
                break;
            default :
                $isControl   = true;
                $hasSiblings = (count($form->getParent()->all()) > 1);
                break;
        }

        if($isControlGroup) {
            /**
             * Control Group Configuration
             */
            $controlGroupClass                      = (isset($options['control_group_attr']['class'])) ? $options['control_group_attr']['class'] : '';
            $options['control_group_attr']['class'] = trim($controlGroupClass . " ControlGroup");
            $view->vars['control_group_attr']       = $options['control_group_attr'];

            $controlGroupLabelClass                       = (isset($options['control_group_label_attr']['class'])) ? $options['control_group_label_attr']['class'] : '';
            $options['control_group_label_attr']['class'] = trim($controlGroupLabelClass . " ControlGroup-label");
            $view->vars['control_group_label_attr']       = $options['control_group_label_attr'];
        }

        if($isRoot || $isControlGroup || $isControl || $isCollection || $isCollectionItem) {
            /**
             * Control Configuration
             */
            $controlClass = (isset($options['control_attr']['class'])) ? $options['control_attr']['class'] : '';
            $controlClass .= " Control";
            if ($hasSiblings) {
                $controlClass .= " Control--hasSiblings";
            }
            $options['control_attr']['class'] = trim($controlClass);
            $view->vars['control_attr'] = $options['control_attr'];

            $controlLabelClass = (isset($options['control_label_attr']['class'])) ? $options['control_label_attr']['class'] : '';
            $options['control_label_attr']['class'] = trim($controlLabelClass . " Control-label");
            $view->vars['control_label_attr'] = $options['control_label_attr'];

            /**
             * Control Input Configuration
             */
            $controlInputClass = (isset($options['control_input_attr']['class'])) ? $options['control_input_attr']['class'] : '';
            $options['control_input_attr']['class'] = trim(
                $controlInputClass .= " Control-input Control-input--" . $formType
            );
            $options['control_input_attr']['class'] = $controlInputClass;
        }

        /**
         * Collection Configuration
         *
         * Custom functionality for collections.
         *
         * This would normally be fired in the collection buildForm but we're hijacking it here
         * which allows us to add extra classes to the control attributes.
         */
        if($isCollection) {

            $collectionClass = (isset($options['collection_attr']['class'])) ? $options['collection_attr']['class'] : '';
            $options['collection_attr']['class'] = trim($collectionClass . " Collection");
            $options['collection_attr']['data-form-widget'] = 'collection';
            $options['collection_attr']['data-collection-allow-add'] = $options['allow_add'];
            $options['collection_attr']['data-collection-allow-delete'] = $options['allow_delete'];

            if ($form->getConfig()->hasAttribute('prototype')) {
                $prototype = $form->getConfig()->getAttribute('prototype');
                $options['collection_attr']['data-prototype-name'] = $prototype->getName();
                $options['prototype'] = $prototype->createView($view);
            }

            $collectionLabelClass                       = (isset($options['collection_label_attr']['class'])) ? $options['collection_label_attr']['class'] : '';
            $options['collection_label_attr']['class'] = trim($collectionLabelClass . " Collection-label");

            $view->vars['collection_attr'] = $options['collection_attr'];
            $view->vars['collection_label_attr'] = $options['collection_label_attr'];
        }

        if($isCollectionItem) {

            $collectionitemClass = (isset($options['collection_item_attr']['class'])) ? $options['collection_item_attr']['class'] : '';
            $collectionitemClass .= " CollectionItem";
            $options['collection_item_attr']['class'] = trim($collectionitemClass);

            $collectionItemLabelClass = (isset($options['collection_item_label_attr']['class'])) ? $options['collection_item_label_attr']['class'] : '';
            $options['collection_item_label_attr']['class'] = trim($collectionItemLabelClass . " CollectionItem-label");

            $view->vars['collection_item_attr'] = $options['collection_item_attr'];
            $view->vars['collection_item_label_attr'] = $options['collection_item_label_attr'];
        }



        /**
         * View helpers
         */
        $view->vars['attr'] = array_merge(
            $view->vars['attr'],
            $options['attr']
        );
        $view->vars['form_type'] = $formType;
        $view->vars['is_root'] = $isRoot;
        $view->vars['is_control_group'] = $isControlGroup;
        $view->vars['is_control'] = $isControl;
        $view->vars['is_collection'] = $isCollection;
        $view->vars['is_collection_item'] = $isCollectionItem;
        $view->vars['has_siblings'] = $hasSiblings;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'control_group_attr' => array(),
                'control_group_label_attr' => array(),
                'control_attr' => array(),
                'control_label_attr' => array(),
                'control_input_attr' => array(),
                'collection_attr' => array(),
                'collection_label_attr' => array(),
                'collection_item_attr' => array(),
                'collection_item_label_attr' => array()
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
