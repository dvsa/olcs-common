<?php

/**
 * Text Max 255
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;

/**
 * Text Max 255 Required no minimum chars
 */
class TextMax255RequiredNoMin extends TextMax255 implements InputProviderInterface
{
    protected $required = true;
    protected $allowEmpty = false;

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
            $specification['validators'][] = new ZendValidator\StringLength(['max' => $this->max]);
        }

        return $specification;
    }
}
