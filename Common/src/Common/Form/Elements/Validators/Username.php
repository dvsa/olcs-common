<?php

/**
 * Ensure the username matches the required criteria
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\StringLength;

/**
 * Ensure the username matches the required criteria
 */
class Username extends StringLength
{
    const USERNAME_LENGTH_MIN = 2;
    const USERNAME_LENGTH_MAX = 40;
    const USERNAME_INVALID = 'usernameInvalid';

    /**
     * Sets validator options
     *
     * @param  array
     */
    public function __construct($options = array())
    {
        $options['min'] = self::USERNAME_LENGTH_MIN;
        $options['max'] = self::USERNAME_LENGTH_MAX;

        $this->messageTemplates[self::USERNAME_INVALID] = 'error.form-validator.username.invalid';

        parent::__construct($options);
    }

    /**
     * Check if username is valid
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (parent::isValid($value)) {
            if (preg_match('/^[0-9a-zA-Z#\$%\'\+\-\/\=\?\^_\.@`\|~",\:;\<\>]*$/', $value)) {
                return true;
            }

            $this->error(self::USERNAME_INVALID);
        }

        return false;
    }
}
