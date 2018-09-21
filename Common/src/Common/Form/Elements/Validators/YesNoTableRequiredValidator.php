<?php

namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

class YesNoTableRequiredValidator extends AbstractValidator
{

    protected $messageTemplates = [];

    private $table;

    public function __construct($options = array())
    {
        $this->table = $options['table'];
        $this->messageTemplates['error'] = $options['message'];

        parent::__construct(array());
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = array())
    {
        if ($context[$this->table]['rows'] == 0 && $value === 'Y') {
            $this->error('error');
            return false;
        }

        return true;
    }
}
