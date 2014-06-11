<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Util\SecureRandom;

class CollectionPrototypeDataType extends AbstractType
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
                'prototype_data' => null
            )
        );
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
