<?php

namespace Nsm\Bundle\PostmarkBundle\Swift\Plugins\Loggers;

use Swift_Events_EventListener as EventListener;
use Swift_Events_TransportExceptionListener as TransportExceptionListener;
use Swift_Events_SendListener as SendListener;

use Swift_Events_ResponseEvent as ResponseEvent;
use Swift_Events_SendEvent as SendEvent;
use Swift_Events_TransportExceptionEvent as TransportExceptionEvent;

class ORMLoggerPlugin implements EventListener, SendListener, TransportExceptionListener
{
    protected $spoolItem;

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param SendEvent $evt
     */
    public function beforeSendPerformed(SendEvent $evt)
    {
    }

    /**
     * Invoked immediately following a response coming back.
     *
     * @param ResponseEvent $evt
     */
    public function responseReceived(ResponseEvent $evt)
    {
        // The ResponseEvent only has a string in the response
        // Do nothing
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param SendEvent $evt
     */
    public function sendPerformed(SendEvent $evt)
    {
        $result = $evt->getResult();
        $message = $evt->getMessage();

        // Log the result
        switch(true){
            case ($result === SendEvent::RESULT_FAILED):
                break;
            case ($result === SendEvent::RESULT_PENDING):
                break;
            case ($result === SendEvent::RESULT_SUCCESS):
                break;
            case ($result === SendEvent::RESULT_TENTATIVE):
                break;
        }
    }

    /**
     * Invoked as a TransportException is thrown in the Transport system.
     *
     * @param TransportExceptionEvent $evt
     */
    public function exceptionThrown(TransportExceptionEvent $evt)
    {
        // Catch Exceptions
    }

}
