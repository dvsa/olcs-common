<?php

/**
 * GenericValidationMessage
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators\Messages;

/**
 * GenericValidationMessage
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericValidationMessage extends AbstractValidationMessage
{
    /**
     * Hold the message
     *
     * @var string
     */
    protected $message;

    /**
     * Setter for message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return a reference of the message
     *
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }
}
