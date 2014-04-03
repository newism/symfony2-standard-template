<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Nsm\Bundle\FormBundle\DataTransformer\DateRangeToArrayTransformer;
use Nsm\Bundle\FormBundle\Form\Validator\DateRangeValidator;
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
                'period',
                'choice',
                array(
                    'required' => false,
                    'empty_data' => '',
                    'choices' => array(
                        'custom' => 'custom',
                        'today' => 'today',
                        'tomorrow' => 'tomorrow',
                        'this_week' => 'this week',
                        'this_month' => 'this month',
                        'this_quarter' => 'this quarter',
                        'this_year' => 'this year',
                        'last_week' => 'last week',
                        'last_month' => 'last month',
                        'last_quarter' => 'last quarter',
                        'last_year' => 'last year',
                    )
                )
            )
            ->add(
                'start',
                $options['start_type'],
                array_merge(
                    array(
                        'help' => 'Test Help',
                        'by_reference' => $options['by_reference'],
                        'required' => $options['required']
                    ),
                    $options['start_options']
                )
            )
            ->add(
                'end',
                $options['end_type'],
                array_merge(
                    array(
                        'by_reference' => $options['by_reference'],
                        'required' => $options['required']
                    ),
                    $options['end_options']
                )
            );

        $builder->addModelTransformer($options['model_transformer']);
        $builder->addEventSubscriber($options['validator']);

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'model_transformer' => null,
                'validator' => null,
                'start_type' => 'datetime',
                'start_options' => array(),
                'end_type' => 'date',
                'end_options' => array(),
                // Don't modify DateRange classes by reference, we treat
                // them like immutable value objects
                'by_reference' => false,
                'error_bubbling' => false,
                // If initialized with a DateRange object, FormType initializes
                // this option to "DateRange". Since the internal, normalized
                // representation is not DateRange, but an array, we need to unset
                // this option.
                'data_class' => null,
//                'data_class' => 'Nsm\Bundle\ApiBundle\Form\Model\DateRange',
                'required' => false
            )
        );

        $resolver->setAllowedValues(
            array(
                'start_type' => array('date', 'datetime'),
                'end_type' => array('date', 'datetime'),
            )
        );

        $resolver->setAllowedTypes(
            array(
                'model_transformer' => 'Symfony\Component\Form\DataTransformerInterface',
                'validator' => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
            )
        );

        // These normalizers lazily create the required objects, if none given.
        $resolver->setNormalizers(
            array(
                'model_transformer' => function (Options $options, $value) {
                        if (null === $value) {
                            $value = new DateRangeToArrayTransformer(new OptionsResolver(), array(
                                'end_type' => $options->get('end_type'),
                                'start_type' => $options->get('start_type')
                            ));
                        }

                        return $value;
                    },
                'validator' => function (Options $options, $value) {
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
