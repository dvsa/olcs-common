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
    protected $max = null;

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
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
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim']
            ],
            'validators' => [
            ]
        ];

        if (!empty($this->max)) {
            $specification['validators'][] = new ZendValidator\StringLength(['min' => 2, 'max' => $this->max]);
        }

        return $specification;
    }
}
