<?php

/**
 * Equal Sum
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * Equal Sum
 *
 * Checks if the value is equal to the sum of the other values defined in the fields option
 * all of the fields (that are set) get appended to the error message translation for dynamic messages
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EqualSum extends AbstractValidator
{
    public function isValid($value, $context = null)
    {
        $contextFields = $this->getOption('fields');

        $sum = 0;
        $message = (string)$this->getOption('errorPrefix');
        $fields = [];

        foreach ($contextFields as $field) {
            if (isset($context[$field])) {
                $fields[] = $field;
                $sum += (int)$context[$field];
            }
        }

        $message .= implode('-', $fields);

        if ((int)$value != $sum) {

            $this->abstractOptions['messageTemplates']['error'] = $message;

            $this->error('error');

            return false;
        }

        return true;
    }
}
