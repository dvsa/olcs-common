<?php
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Textarea as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated This only gets used once in \Olcs\Form\Model\Fieldset\ReverseTransactionDetails
 *             We must look into removing it and replacing with standard MultiCheckbox.
 *             Reference: OLCS-15198
 *
 * Textarea
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
