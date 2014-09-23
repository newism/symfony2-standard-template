<?php

namespace Nsm\Bundle\FormBundle\DataTransformer;

use Nsm\Bundle\FormBundle\Form\Model\DateRange;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeToArrayTransformer implements DataTransformerInterface
{
    /**
     * Model to Norm
     *
     * @param mixed $dateRange
     *
     * @return array|mixed|string
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function transform($dateRange)
    {
        if (null === $dateRange) {
            return null;
        }

        if (!$dateRange instanceof DateRange) {
            throw new UnexpectedTypeException($dateRange, 'DateRange');
        }

        return array(
            'period' => 0,
            'start' => $dateRange->getStart(),
            'end' => $dateRange->getEnd()
        );
    }

    /**
     * Norm to Model
     *
     * @param mixed $value
     *
     * @return mixed|DateRange|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        /**
         * Todo: This methods are not correct.
         */
        switch($value['period']) {
            case 'custom':
                $start = $value['start'];
                $end = $value['end'];
                break;
            case 'today':
                $start = new \DateTime('today');
                $end = new \DateTime('today');
                break;
            case 'tomorrow':
                $start = new \DateTime('tomorrow');
                $end = new \DateTime('tomorrow');
                break;
            case 'this_week':
                $start = new \DateTime('this week');
                $end = new \DateTime('this week');
                break;
            case 'this_month':
                $start = new \DateTime('this month');
                $end = new \DateTime('this month');
                break;
            case 'this_year':
                $start = new \DateTime('this month');
                $end = new \DateTime('this year');
                break;
            case 'last_week':
                $start = new \DateTime('last week');
                $end = new \DateTime('last week');
                break;
            case 'last_month':
                $start = new \DateTime('last month');
                $end = new \DateTime('last month');
                break;
            case 'last_year':
                $start = new \DateTime('last month');
                $end = new \DateTime('last year');
                break;
            default:
                throw new TransformationFailedException();
                break;
        }

        // Check here if period is custom and calculate dates
        return new DateRange($period, $start, $end);
    }
}
