<?php

namespace Nsm\Bundle\ApiBundle\Form\DataTransformer;

use Nsm\Bundle\ApiBundle\Form\Model\DateRange;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeViewTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param OptionsResolverInterface $resolver
     * @param array                    $options
     */
    public function __construct(OptionsResolverInterface $resolver, array $options = array())
    {
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'include_end' => true,
            )
        );

        $resolver->setAllowedValues(
            array(
                'include_end' => array(true, false),
            )
        );
    }

    /**
     * @param $value
     *
     * @return DateRangeInterface|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        if (!$value instanceof DateRange) {
            throw new UnexpectedTypeException($value, 'DateRange');
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return DateRange|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof DateRange) {
            throw new UnexpectedTypeException($value, 'DateRange');
        }
        
        if(null === $value->start && null === $value->end) {
            return null;
        }

        if (null !== $value->end && $this->options['include_end']) {
            $value->end->setTime(23, 59, 59);
        }

        return $value;
    }
}
