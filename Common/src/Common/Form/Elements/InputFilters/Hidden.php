<?php

/**
 * Text
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Hidden as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Hidden extends ZendElement implements InputProviderInterface
{
    protected $required = false;
    protected $continueIfEmpty = false;
    protected $allowEmpty = true;
    protected $max = null;

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
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim']
            ],
            'validators' => $this->getValidators()
        ];

        if (!empty($this->max)) {
            $specification['validators'][] = new ZendValidator\StringLength(['min' => 2, 'max' => $this->max]);
        }

        return $specification;
    }
}
