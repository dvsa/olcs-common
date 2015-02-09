<?php

/**
 * File Required Validator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * File Required Validator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EvidenceRequiredValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Please upload evidence of %label%'
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
    protected $label;

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
     * @param mixed $value
     * @param array $context
     */
    public function isValid($value, $context = null)
    {
        if (empty($context['file']['list'])) {
            $this->error('required');
            return false;
        }
        return true;
    }
}
