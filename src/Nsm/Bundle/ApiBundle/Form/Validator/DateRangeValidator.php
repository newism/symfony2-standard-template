<?php

namespace Nsm\Bundle\ApiBundle\Form\Validator;

use DateTime;
use Nsm\Bundle\ApiBundle\Form\Model\DateRange;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateRangeValidator implements EventSubscriberInterface
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
                'allow_end_in_past' => true,
                'allow_single_day'  => true,
            )
        );

        $resolver->setAllowedValues(
            array(
                'allow_end_in_past' => array(true, false),
                'allow_single_day'  => array(true, false),
            )
        );
    }

    /**
     * @param FormEvent $event
     */
    public function onPostBind(FormEvent $event)
    {
        $form = $event->getForm();

        /* @var $dateRange DateRange */
        $dateRange = $form->getNormData();

        if(null === $dateRange) {
            return;
        }

        if (null !== $dateRange->start
            && null !== $dateRange->end
            && $dateRange->start > $dateRange->end
        ) {
            $form->addError(new FormError('date_range.invalid.end_before_start'));
        }

        if (
            null !== $dateRange->start
            && null !== $dateRange->end
            && false === $this->options['allow_single_day']
            && $dateRange->start->format('Y-m-d') === $dateRange->end->format('Y-m-d')
        ) {
            $form->addError(new FormError('date_range.invalid.single_day'));
        }

        if (null !== $dateRange->end
            && false === $this->options['allow_end_in_past']
            && $dateRange->end < new DateTime()
        ) {
            $form->addError(new FormError('date_range.invalid.end_in_past'));
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_BIND => 'onPostBind',
        );
    }
}
