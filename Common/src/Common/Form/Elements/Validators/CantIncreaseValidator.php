<?php

/**
 * Cant Increase Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;
use Common\Form\Elements\Validators\Messages\GenericValidationMessage;

/**
 * Cant Increase Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CantIncreaseValidator extends AbstractValidator
{
    /**
     * Message key constants
     */
    const CANT_INCREASE = 1;

    /**
     * Holds the previous value
     *
     * @var int
     */
    private $previousValue;

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::CANT_INCREASE => 'cant-increase-validator-'
    );

    /**
     * Set the message suffix so we can translate it
     *
     * @param string $message
     */
    public function setGenericMessage($message)
    {
        $messageObject = new GenericValidationMessage();
        $messageObject->setShouldTranslate(false);
        $messageObject->setShouldEscape(false);
        $messageObject->setMessage($message);

        $this->setMessage($messageObject, self::CANT_INCREASE);
    }

    /**
     * Constructs and returns a validation failure message with the given message key and value.
     *
     * Returns null if and only if $messageKey does not correspond to an existing template.
     *
     * If a translator is available and a translation exists for $messageKey,
     * the translation will be used.
     *
     * @param  string              $messageKey
     * @param  string|array|object $value
     * @return string
     */
    protected function createMessage($messageKey, $value)
    {
        if (!isset($this->abstractOptions['messageTemplates'][$messageKey])) {
            return null;
        }

        return $this->abstractOptions['messageTemplates'][$messageKey];
    }

    /**
     * Set previous value
     *
     * @param int $value
     */
    public function setPreviousValue($value)
    {
        $this->previousValue = $value;
    }

    /**
     * Check if the value has increased or not
     *
     * @param int $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (is_numeric($value) && $value > $this->previousValue) {

            $this->error(self::CANT_INCREASE);

            return false;
        }

        return true;
    }
}
