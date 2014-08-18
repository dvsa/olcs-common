<?php

/**
 * SingleCheckbox element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

/**
 * SingleCheckbox element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SingleCheckbox extends Checkbox
{
    protected $required = false;
    protected $continueIfEmpty = false;
    protected $allowEmpty = true;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

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
            'name' => $this->getName(),
            'required' => $this->required,
            'continue_if_empty' => $this->continueIfEmpty,
            'allow_empty' => $this->allowEmpty,
            'validators' => $this->getValidators()
        ];

        return $specification;
    }
}
