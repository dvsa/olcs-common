<?php

namespace Common\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class OneOf
 * @package Common\Validator
 */
class OneOf extends AbstractValidator
{
    const PROVIDE_ONE = 'provide_one';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::PROVIDE_ONE         => 'Please provide at least one value',
    );

    /**
     * @var
     */
    protected $fields;

    /**
     * @param mixed $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $options
     * @return \Zend\Validator\AbstractValidator
     */
    public function setOptions($options = [])
    {
        if (isset($options['fields'])) {
            $this->setFields($options['fields']);
        }

        // provides an easier method to override the default message, which will be a common use case.
        if (isset($options['message'])) {
            $this->abstractOptions['messageTemplates'][self::PROVIDE_ONE] = $options['message'];
        }

        return parent::setOptions($options);
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param  mixed $context
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value, $context = null)
    {
        $valid = false;
        foreach ($this->getFields() as $field) {
            if (isset($context[$field]) && !empty($context[$field])) {
                $valid = true;
                break;
            }
        }

        if (!$valid) {
            $this->error(self::PROVIDE_ONE);
        }

        return $valid;
    }
}
