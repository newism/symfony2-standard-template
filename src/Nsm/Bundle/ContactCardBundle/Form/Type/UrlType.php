<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Url Form Type
 */
class UrlType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'url',
            'text',
            array(
                'label' => 'url',
                'required' => false,
            )
        );

        $builder->add(
            'type',
            'text',
            array(
                'label' => 'type',
                'required' => false,
            )
        );

        $builder->add(
            'label',
            'text',
            array(
                'label' => 'label',
                'required' => false,
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\ContactCardBundle\Entity\Url',
                'cascade_validation' => true,
                'error_bubbling' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'url';
    }
}
