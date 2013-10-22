<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Form\DataTransformer\DateRangeViewTransformer;
use Nsm\Bundle\ApiBundle\Form\Validator\DateRangeValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'start_date',
                'date',
                array_merge(
                    array(
                        'property_path' => 'start'
                    ),
                    $options['start_options']
                )
            )
            ->add(
                'end_date',
                'date',
                array_merge(
                    array(
                        'property_path' => 'end',
                    ),
                    $options['end_options']
                )
            );

        $builder->addViewTransformer($options['transformer']);
        $builder->addEventSubscriber($options['validator']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'    => 'Nsm\Bundle\ApiBundle\Form\Model\DateRange',
                'transformer'   => null,
                'validator'     => null,
                'error_bubbling' => false,
                'end_options'   => array(),
                'start_options' => array()
            )
        );

        $resolver->setAllowedTypes(
            array(
                'transformer' => 'Symfony\Component\Form\DataTransformerInterface',
                'validator'   => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            )
        );

        // These normalizers lazily create the required objects, if none given.
        $resolver->setNormalizers(
            array(
                'transformer' => function (Options $options, $value) {
                    if (null === $value) {
                        $value = new DateRangeViewTransformer(new OptionsResolver());
                    }

                    return $value;
                },
                'validator'   => function (Options $options, $value) {
                    if (null === $value) {
                        $value = new DateRangeValidator(new OptionsResolver());
                    }

                    return $value;
                }
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'date_range';
    }
}
