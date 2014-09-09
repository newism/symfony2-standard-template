<?php

namespace Nsm\Bundle\FormBundle\Form\EventListener;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

class EnsureTargetPathFieldListener
{
    private $factory;
    private $name;
    private $choices;

    public function __construct(FormFactoryInterface $factory, $name, $choices)
    {
        $this->factory = $factory;
        $this->name = $name;
        $this->choices = $choices;
    }

    public function ensureTargetPathField(FormEvent $event)
    {
        $form = $event->getForm();

        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            $form->add(
                $this->name,
                'choice',
                array(
                    'choices' => $this->choices,
                    'required' => false,
                    'mapped' => false
                )
            );
        }
    }
}
