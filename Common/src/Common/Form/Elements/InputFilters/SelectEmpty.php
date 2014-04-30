<?php

/**
 * Select with empty allowed
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Select as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Select with empty allowed
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class SelectEmpty extends ZendElement implements InputProviderInterface
{
    protected $required = false;
    protected $continueIfEmpty = true;
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
            'required' => $this->required,
            'continue_if_empty' => $this->continueIfEmpty,
            'allow_empty' => $this->allowEmpty,
            'validators' => $this->getValidators()
        ];

        return $specification;
    }
}
