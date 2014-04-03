<?php

namespace Nsm\Bundle\PostmarkBundle\Swift\Spool;

use Swift_ConfigurableSpool as ConfigurableSpool;
use Swift_Mime_Message as Mime_Message;
use Swift_IoException as IoException;
use Swift_Transport as Transport;

class ORMSpool extends ConfigurableSpool
{
    private $started;

    /**
     * Starts this Spool mechanism.
     *
     * @return $this
     */
    public function start()
    {
        $this->started = true;

        return $this;
    }

    /**
     * Stops this Spool mechanism.
     */
    public function stop()
    {
        $this->started = false;
    }

    /**
     * Tests if this Spool mechanism has started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @param Mime_Message $message
     *
     * @return bool|void
     * @throws IoException
     */
    public function queueMessage(Mime_Message $message)
    {
        // Save to ORM
        throw new IoException('Unable to create a file for enqueuing Message');
    }

    /**
     * Sends messages using the given transport instance.
     *
     * @param Transport $transport        A transport instance
     * @param string[]  $failedRecipients An array of failures by-reference
     *
     * @return integer The number of sent e-mail's
     */
    public function flushQueue(Transport $transport, &$failedRecipients = null)
    {
        $count            = 0;
        $failedRecipients = (array)$failedRecipients;

        // Find all files in ORM
        // Loop over each one
        $messages = array();

        foreach ($messages as $message) {
            $count += $transport->send($message, $failedRecipients);
        }

        return $count;
    }
} 
