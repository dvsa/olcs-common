<?php

/**
 * Text
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Text as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Text extends ZendElement implements InputProviderInterface
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
     * Setter for allow empty
     *
     * @param boolean $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * Setter for max
     *
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
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
            $specification['validators'][] = [
                'name' => 'Zend\Validator\StringLength',
                'options' => ['min' => 2, 'max' => $this->max]
            ];
        }

        return $specification;
    }
}
