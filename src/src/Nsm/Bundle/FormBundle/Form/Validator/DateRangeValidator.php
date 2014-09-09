<?php

namespace Nsm\Bundle\FormBundle\Form\Validator;

use DateTime;
use Nsm\Bundle\FormBundle\Form\Model\DateRange;
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
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        /* @var $dateRange DateRange */
        $dateRange = $form->getData();

        if(null === $dateRange) {
            return;
        }

        if (null !== $dateRange->getStart()
            && null !== $dateRange->getEnd()
            && $dateRange->getStart() > $dateRange->getEnd()
        ) {
            $form->addError(new FormError('date_range.invalid.end_before_start'));
        }

        if (
            null !== $dateRange->getStart()
            && null !== $dateRange->getEnd()
            && false === $this->options['allow_single_day']
            && $dateRange->getStart()->format('Y-m-d') === $dateRange->getEnd()->format('Y-m-d')
        ) {
            $form->addError(new FormError('date_range.invalid.single_day'));
        }

        if (null !== $dateRange->getEnd()
            && false === $this->options['allow_end_in_past']
            && $dateRange->getEnd() < new DateTime()
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
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        );
    }
}
