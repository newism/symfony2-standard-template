<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Form\Model\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CriteriaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'attribute',
                'choice',
                array_merge(
                    array(
                        'required' => true
                    ),
                    $options['attribute_options']
                )
            )
            ->add(
                'expression',
                'choice',
                array_merge(
                    array(
                        'required' => true,
                        'choices'  => Criteria::getExpressions()
                    ),
                    $options['expression_options']
                )
            )
            ->add(
                'value',
                'text',
                array_merge(
                    array(
                        'required' => false,
                    ),
                    $options['value_options']
                )
            );

//        $builder->addViewTransformer($options['transformer']);
//        $builder->addEventSubscriber($options['validator']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'Nsm\Bundle\ApiBundle\Form\Model\Criteria',
                'transformer'        => null,
                'validator'          => null,
                'error_bubbling'     => false,
                'attribute_options'  => array(),
                'expression_options' => array(),
                'value_options' => array(),
                'by_reference'       => false
            )
        );

        $resolver->setAllowedValues(array());

//        $resolver->setAllowedTypes(
//            array(
//                'transformer' => 'Symfony\Component\Form\DataTransformerInterface',
//                'validator'   => 'Symfony\Component\EventDispatcher\EventSubscriberInterface',
//            )
//        );

        // These normalizers lazily create the required objects, if none given.
//        $resolver->setNormalizers(
//            array(
//                'transformer' => function (Options $options, $value) {
//                        if (null === $value) {
//                            $value = new DateRangeViewTransformer(new OptionsResolver(), array(
//                                'end_type'   => $options->get('end_type'),
//                                'start_type' => $options->get('start_type')
//                            ));
//                        }
//
//                        return $value;
//                    },
//                'validator'   => function (Options $options, $value) {
//                        if (null === $value) {
//                            $value = new DateRangeValidator(new OptionsResolver());
//                        }
//
//                        return $value;
//                    }
//            )
//        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'criteria';
    }
}
