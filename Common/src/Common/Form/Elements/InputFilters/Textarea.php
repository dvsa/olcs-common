<?php

/**
 * Textarea
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Textarea as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Textarea
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Textarea extends ZendElement implements InputProviderInterface
{
    protected $continueIfEmpty = false;
    protected $allowEmpty = false;
    protected $required = false;
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
            'required' => $this->required ?: false,
            'continueIfEmpty' => $this->continueIfEmpty ?: false,
            'allowEmpty' => $this->allowEmpty ?: false,
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim']
            ],
            'validators' => $this->getValidators()
        ];

        if (!empty($this->max)) {
            $specification['validators'][] = [
                'name' => 'Zend\Validator\StringLength',
                'options' => ['min' => 5, 'max' => $this->max]
            ];
        }

        return $specification;
    }
}
