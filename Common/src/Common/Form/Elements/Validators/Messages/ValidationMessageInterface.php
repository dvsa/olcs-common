<?php

/**
 * ValidationMessageInterface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators\Messages;

/**
 * ValidationMessageInterface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ValidationMessageInterface
{
    /**
     * Return the message text
     *
     * @return string
     */
    public function getMessage();

    /**
     * Check if the message should be translated
     *
     * @return boolean
     */
    public function shouldTranslate();

    /**
     * Set whether or not the message should be translated
     *
     * @param boolean $flag
     */
    public function setShouldTranslate($flag);

    /**
     * Check if the message should be escaped
     *
     * @return boolean
     */
    public function shouldEscape();

    /**
     * Set whether or not the message should be escaped
     *
     * @param boolean $flag
     */
    public function setShouldEscape($flag);
}
