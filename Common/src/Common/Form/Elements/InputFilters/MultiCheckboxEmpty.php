<?php

/**
 * Multi checkbox with empty allowed
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\MultiCheckbox;
use Zend\InputFilter\InputProviderInterface;

/**
 * Multi checkbox with empty allowed
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class MultiCheckboxEmpty extends MultiCheckbox implements InputProviderInterface
{
    protected $required = false;
    protected $continueIfEmpty = true;
    protected $allowEmpty = true;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array();
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'required' => $this->required,
            'continue_if_empty' => $this->continueIfEmpty,
            'allow_empty' => $this->allowEmpty,
            'validators' => $this->getValidators()
        ];

        return $specification;
    }
}
