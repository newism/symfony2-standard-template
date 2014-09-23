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
    private function formIsRoot(FormInterface $form = null)
    {
        return $form->isRoot();
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formParentIsRoot(FormInterface $form = null)
    {
        $formParent = $form->getParent();

        return (null === $formParent) ? false : $this->formIsRoot($formParent);
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formIsControlGroup(FormInterface $form = null)
    {
        return (true === $this->formParentIsRoot($form) || true === $this->formParentIsCollectionItem($form));
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formIsCollection(FormInterface $form = null)
    {
        return in_array($form->getConfig()->getType()->getName(), array("nsm_collection", "collection"));
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formIsCollectionItem(FormInterface $form = null)
    {
        return $form->getConfig()->getOption('collection_item', false);
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formParentIsCollectionItem(FormInterface $form = null)
    {
        $formParent = $form->getParent();

        return (null === $formParent) ? false : $this->formIsCollectionItem($formParent);
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formIsCollectionItemControlGroup(FormInterface $form = null)
    {
        $formParent = $form->getParent();

        return (null === $formParent) ? false : $this->formIsCollectionItem($formParent);
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formIsControl(FormInterface $form = null)
    {
        if ($this->formIsRoot($form)
            || $this->formIsControlGroup($form)
            || $this->formIsCollection($form)
            || $this->formIsCollectionItem($form)
        ) {
            return false;
        }

        $formParent = $form->getParent();
        $formParentIsControlGroup = $this->formIsControlGroup($formParent);
        $formParentIsControl = $this->formIsControl($formParent);

        return (null === $formParent) ? false : ($formParentIsControlGroup || $formParentIsControl);
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    private function formHasSiblings(FormInterface $form = null)
    {
        $formParent = $form->getParent();

        return (null === $formParent) ? false : $formParent->getConfig()->getCompound();
    }

    /**
     * Determines if the form is one of the following:
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $formType = $form->getConfig()->getType()->getName();

        $isRoot = $this->formIsRoot($form);
        $isControlGroup = $this->formIsControlGroup($form);
        $isCollection = $this->formIsCollection($form);
        $isCollectionItem = $this->formIsCollectionItem($form);
        $isCollectionItemControlGroup = $this->formIsCollectionItemControlGroup($form);
        $hasSiblings = $this->formHasSiblings($form);
        $isCompound = $form->getConfig()->getCompound();

        $isControl = $this->formisControl($form);

        $id = $view->vars["id"];
        $view->vars["_id"] = $id;
        $view->vars["id"] = false;

        if ($isRoot) {
            $formClass = "Form";
            $formClass .= " Form--" . $view->vars['name'];
            $formClass .= " Form--" . $options['layout'];
            $formClass .= " ";
            $formClass .= (isset($options['form_attr']['class'])) ? $options['form_attr']['class'] : '';
            $options['form_attr']['class'] = trim($formClass);

            $view->vars['form_attr'] = $options['form_attr'];

            $controlGroupCollectionClass = "ControlGroupCollection";
            $controlGroupCollectionClass .= " ControlGroupCollection--" . $options['layout'];
            $controlGroupCollectionClass .= " ";
            $controlGroupCollectionClass .= (isset($options['control_group_collection_attr']['class'])) ? $options['control_group_collection_attr']['class'] : '';
            $options['control_group_collection_attr']['class'] = trim($controlGroupCollectionClass);
            $view->vars['control_group_collection_attr'] = $options['control_group_collection_attr'];
        }

        if ($isControlGroup) {
            /**
             * Control Group Configuration
             */
            $controlGroupClass = "ControlGroup";
            $controlGroupClass .= " ControlGroup--" . (($isCompound) ? "multiControl" : "singleControl");
            $controlGroupClass .= " ControlGroup--" . $formType;
            $controlGroupClass .= " ControlGroup--" . $view->vars['name'];
            $controlGroupClass .= " ";
            $controlGroupClass .= (isset($options['control_group_attr']['class'])) ? $options['control_group_attr']['class'] : '';
            $options['control_group_attr']['class'] = trim($controlGroupClass);

            $view->vars['control_group_attr'] = $options['control_group_attr'];

            $controlGroupLabelClass = "ControlGroup-label";
            $controlGroupLabelClass .= " ";
            $controlGroupLabelClass .= (isset($options['control_group_label_attr']['class'])) ? $options['control_group_label_attr']['class'] : '';
            $options['control_group_label_attr']['class'] = $controlGroupLabelClass;

            $view->vars['control_group_label_attr'] = $options['control_group_label_attr'];
        }

        if ($isRoot || $isControlGroup || $isControl || $isCollection || $isCollectionItem) {
            /**
             * Control Configuration
             */
            $controlClass = " Control";
            $controlClass .= " Control--" . $formType;
            $controlClass .= " Control--" . $view->vars['name'];
            $controlClass .= " ";
            $controlClass .= (isset($options['control_attr']['class'])) ? $options['control_attr']['class'] : '';
            $options['control_attr']['class'] = trim($controlClass);

            $options['control_attr']['data-control-full-name'] = $view->vars['full_name'];
            $options['control_attr']['data-control-name'] = $view->vars['name'];

            $view->vars['control_attr'] = $options['control_attr'];

            $controlLabelClass = "Control-label";
            $controlLabelClass .= " ";
            $controlLabelClass .= (isset($options['control_label_attr']['class'])) ? $options['control_label_attr']['class'] : '';
            $options['control_label_attr']['class'] = trim($controlLabelClass);

            $view->vars['control_label_attr'] = $options['control_label_attr'];

            /**
             * Control Input Configuration
             */
            $controlInputClass = " Control-input";
            $controlInputClass .= " Control-input--" . $formType;
            $controlInputClass .= " Control-input--" . $view->vars['name'];
            $controlInputClass .= " ";
            $controlInputClass .= (isset($options['control_input_attr']['class'])) ? $options['control_input_attr']['class'] : '';
            $options['control_input_attr']['class'] = trim($controlInputClass);

            $view->vars['control_input_attr'] = $options['control_input_attr'];
        }

        /**
         * Collection Configuration
         *
         * Custom functionality for collections.
         *
         * This would normally be fired in the collection buildForm but we're hijacking it here
         * which allows us to add extra classes to the control attributes.
         */
        if ($isCollection) {

            $collectionClass = "Collection";
            $collectionClass .= " Collection--" . ($options['layout'] ? $options['layout'] : 'stacked');
            $collectionClass .= " ";
            $collectionClass .= (isset($options['collection_attr']['class'])) ? $options['collection_attr']['class'] : '';
            $options['collection_attr']['class'] = trim($collectionClass);

            $options['collection_attr']['data-widget'] = 'collection';
            $options['collection_attr']['data-collection'] = true;
            $options['collection_attr']['data-collection-id'] = $id;
            $options['collection_attr']['data-collection-full-name'] = $view->vars['full_name'];
            $options['collection_attr']['data-collection-name'] = $view->vars['name'];
            $options['collection_attr']['data-collection-allow-add'] = $options['allow_add'];
            $options['collection_attr']['data-collection-allow-delete'] = $options['allow_delete'];
            $options['collection_attr']['data-collection-allow-sort'] = $options['allow_sort'];

            if ($options['allow_sort']) {
                $options['collection_attr']['data-collection-sort-control'] = $options['sort_control'];
            }

            if ($form->getConfig()->hasAttribute('prototype')) {
                $prototype = $form->getConfig()->getAttribute('prototype');
                $options['collection_attr']['data-collection-prototype-name'] = $prototype->getName();
                $options['prototype'] = $prototype->createView($view);
            }

            $collectionLabelClass = (isset($options['collection_label_attr']['class'])) ? $options['collection_label_attr']['class'] : '';
            $options['collection_label_attr']['class'] = trim($collectionLabelClass . " Collection-label");

            $view->vars['collection_attr'] = $options['collection_attr'];
            $view->vars['collection_label_attr'] = $options['collection_label_attr'];
        }

        if ($isCollectionItem) {

            $collectionitemClass = "CollectionItem";
            $collectionitemClass .= " ";
            $collectionitemClass .= (isset($options['collection_item_attr']['class'])) ? $options['collection_item_attr']['class'] : '';
            $options['collection_item_attr']['class'] = trim($collectionitemClass);

            $collectionItemLabelClass = "CollectionItem-label";
            $collectionItemLabelClass .= " ";
            $collectionItemLabelClass .= (isset($options['collection_item_label_attr']['class'])) ? $options['collection_item_label_attr']['class'] : '';
            $options['collection_item_label_attr']['class'] = trim($collectionItemLabelClass);

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
        $view->vars['is_collection_item_control_group'] = $isCollectionItemControlGroup;
        $view->vars['has_siblings'] = $hasSiblings;

        $view->vars['collection_layout'] = $isCollection ? $form->getConfig()->getOption('layout') : null;
        $view->vars['collection_item_layout'] = $isCollectionItem ? $form->getConfig()->getOption(
            'collection_item_layout'
        ) : null;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'form_attr' => array(),
                'control_group_collection_attr' => array(),
                'control_group_attr' => array(),
                'control_group_label_attr' => array(),
                'control_attr' => array(),
                'control_label_attr' => array(),
                'control_input_attr' => array(),
                'collection_attr' => array(),
                'collection_label_attr' => array(),
                'collection_item_attr' => array(),
                'collection_item_label_attr' => array(),
                'collection_item' => false,
                'collection_item_layout' => null,
                'layout' => 'stacked'
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
