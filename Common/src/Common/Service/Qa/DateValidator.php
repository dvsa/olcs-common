<?php

namespace Common\Service\Qa;

use Laminas\Validator\Date as ZendDateValidator;

class DateValidator extends ZendDateValidator
{
    /**
     * {@inheritdoc}
     */
    protected function error($messageKey, $value = null)
    {
        // suppress the creation of the FALSEFORMAT error message to prevent an invalid date from generating two
        // error messages

        if ($messageKey == ZendDateValidator::FALSEFORMAT) {
            return;
        }

        parent::error($messageKey, $value);
    }
}
