<?php

/**
 * TableRequiredValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * TableRequiredValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableRequiredValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Please add a %label%'
    );

    /**
     * Message variables
     *
     * @var array
     */
    protected $messageVariables = array(
        'label' => 'label'
    );

    /**
     * Holds the label
     *
     * @var string
     */
    protected $label = 'record to the table';

    /**
     * Set the label variable
     *
     * @param string $label
     */
    protected function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Custom validation for tachograph analyser
     *
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        unset($value);

        if (empty($context['action']) && $context['rows'] < 1) {

            $this->error('required');

            return false;
        }

        return true;
    }
}
