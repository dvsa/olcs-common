<?php

/**
 * Abstract Validation Message
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators\Messages;

/**
 * Abstract Validation Message
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractValidationMessage implements ValidationMessageInterface
{
    /**
     * Whether the message should be translated
     *
     * @var boolean
     */
    private $shouldTranslate = true;

    /**
     * Whether the message should be escaped
     *
     * @var boolean
     */
    private $shouldEscape = true;

    /**
     * Get the message
     *
     * @return string
     */
    abstract public function getMessage();

    /**
     * Check if the message should be translated
     *
     * @return boolean
     */
    public function shouldTranslate()
    {
        return $this->shouldTranslate;
    }

    /**
     * Set whether or not the message should be translated
     *
     * @param boolean $flag
     */
    public function setShouldTranslate($flag)
    {
        $this->shouldTranslate = $flag;
    }

    /**
     * Check if the message should be escaped
     *
     * @return boolean
     */
    public function shouldEscape()
    {
        return $this->shouldEscape;
    }

    /**
     * Set whether or not the message should be escaped
     *
     * @param boolean $flag
     */
    public function setShouldEscape($flag)
    {
        $this->shouldEscape = $flag;
    }
}
