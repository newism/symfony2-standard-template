<?php

namespace ClubEvo\Bundle\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Person Form Type
 */
class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('familyName');
        $builder->add('givenName');
        $builder->add('additionalNames');
        $builder->add('honorificPrefix');
        $builder->add('honorificSuffix');
        $builder->add('nickName');
        $builder->add('birthday');
        $builder->add('gender');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'ClubEvo\Bundle\ApiBundle\Entity\Person',
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
        return 'person';
    }
}
