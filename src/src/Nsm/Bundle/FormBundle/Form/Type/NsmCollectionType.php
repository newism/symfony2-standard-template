<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Util\SecureRandom;

class NsmCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $prototypeFormBuilder = $builder->getAttribute('prototype');

        if (null !== $prototypeFormBuilder) {
            /** @var $prototypeFormBuilder Form */
            $prototypeFormBuilder->setData($options['prototype_data']);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'by_reference' => false,
                'prototype_data' => null,
                'allow_sort' => true,
                'sort_control' => 'order',
                /**
                 * The basic way in which this field should be rendered. Can be one of the following:
                 *
                 * - table: Renders collection items in a table
                 * - form: Renders collection items in div layout
                 */
                'layout' => 'table'
            )
        );

        $resolver->setNormalizers(array(
            /**
             * Normalise the options for the child form (collection item)
             */
            'options' => function (Options $options, $value) {
                $value['collection_item'] = true;
                $value['collection_item_layout'] = $options['layout'];

                return $value;
            },
        ));
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'allow_sort'    => $options['allow_sort']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nsm_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }
}
