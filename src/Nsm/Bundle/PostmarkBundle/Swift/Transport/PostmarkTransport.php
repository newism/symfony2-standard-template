<?php

namespace Nsm\Bundle\PostmarkBundle\Swift\Transport;

use Nsm\Bundle\PostmarkBundle\Postmark\MessageResponse;
use Swift_Events_EventDispatcher as EventDispatcher;
use Swift_Events_EventListener as EventListener;
use Swift_Events_SendEvent as SendEvent;
use Swift_Mime_Message as Mime_Message;
use Swift_Transport as Transport;
use Swift_TransportException as TransportException;


class PostmarkTransport implements Transport
{
    private $started;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param                              $postmarkApiToken
     * @param bool                         $useHttps
     */
    public function __construct(
        EventDispatcher $eventDispatcher,
        $postmarkApiToken,
        $useHttps = true
    ) {
        $this->eventDispatcher  = $eventDispatcher;
        $this->postmarkApiToken = $postmarkApiToken;
        $this->scheme           = (true === $useHttps) ? 'https://' : 'http://';
    }

    /**
     * Test if this Transport mechanism has started.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Start this Transport mechanism.
     */
    public function start()
    {
        if (!$this->started) {
            if ($evt = $this->eventDispatcher->createTransportChangeEvent($this)) {
                $this->eventDispatcher->dispatchEvent($evt, 'beforeTransportStarted');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            $this->started = true;

            if ($evt) {
                $this->eventDispatcher->dispatchEvent($evt, 'transportStarted');
            }
        }
    }

    /**
     * Stop this Transport mechanism.
     */
    public function stop()
    {
        if ($this->started) {
            if ($evt = $this->eventDispatcher->createTransportChangeEvent($this)) {
                $this->eventDispatcher->dispatchEvent($evt, 'beforeTransportStopped');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            $this->started = false;

            if ($evt) {
                $this->eventDispatcher->dispatchEvent($evt, 'transportStopped');
            }
        }
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Mime_Message $message
     * @param null               $failedRecipients
     *
     * @return int
     */
    public function send(Mime_Message $message, &$failedRecipients = null)
    {
        $sent             = 0;
        $failedRecipients = (array)$failedRecipients;

        if ($sendEvent = $this->eventDispatcher->createSendEvent($this, $message)) {
            $this->eventDispatcher->dispatchEvent($sendEvent, 'beforeSendPerformed');
            if ($sendEvent->bubbleCancelled()) {
                return 0;
            }
        }

        // Sample Postmark Response
        $response = new MessageResponse(
            array(
                "ErrorCode"   => 0,
                "Message"     => "OK",
                "MessageID"   => "b7bc2f4a-e38e-4336-af7d-e6c392c2f817",
                "SubmittedAt" => "2010-11-26T12:01:05.1794748-05:00",
                "To"          => "receiver@example.com"
            )
        );

        $success = true;
        $tentativeSuccess = true;

        if ($responseEvent = $this->eventDispatcher->createResponseEvent($this, (string)$response, $success)) {
            $this->eventDispatcher->dispatchEvent($responseEvent, 'responseReceived');
            if ($responseEvent->bubbleCancelled()) {
                return 0;
            }
        }

        if ($sendEvent) {
            if ($success) {
                $sendEvent->setResult(SendEvent::RESULT_SUCCESS);
            } elseif ($tentativeSuccess) {
                $sendEvent->setResult(SendEvent::RESULT_TENTATIVE);
            } else {
                $sendEvent->setResult(SendEvent::RESULT_FAILED);
                $sent = 0;
            }
            $sendEvent->setFailedRecipients($failedRecipients);
            $this->eventDispatcher->dispatchEvent($sendEvent, 'sendPerformed');
        }

        $message->generateId(); //Make sure a new Message ID is used

        return $sent;
    }

    /**
     * Register a plugin in the Transport.
     *
     * @param EventListener $plugin
     */
    public function registerPlugin(EventListener $plugin)
    {
        $this->eventDispatcher->bindEventListener($plugin);
    }

    /**
     * Throw a TransportException, first sending it to any listener
     *
     * @param TransportException $e
     *
     * @throws TransportException
     */
    protected function throwException(TransportException $e)
    {
        if ($evt = $this->eventDispatcher->createTransportExceptionEvent($this, $e)) {
            $this->eventDispatcher->dispatchEvent($evt, 'exceptionThrown');
            if (!$evt->bubbleCancelled()) {
                throw $e;
            }
        } else {
            throw $e;
        }
    }

} 
