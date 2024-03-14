<?php
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\Textarea as LaminasElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

/**
 * @deprecated This only gets used once in \Olcs\Form\Model\Fieldset\ReverseTransactionDetails
 *             We must look into removing it and replacing with standard MultiCheckbox.
 *             Reference: OLCS-15198
 *
 * Textarea
 */
class Textarea extends LaminasElement implements InputProviderInterface
{
    protected $continueIfEmpty = false;
    protected $allowEmpty = false;
    protected $required = false;
    protected $max = null;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $specification = [
            'name' => $this->getName(),
            'required' => $this->required,
            'continue_if_empty' => $this->continueIfEmpty,
            'allow_empty' => $this->allowEmpty,
            'filters' => [
                ['name' => \Laminas\Filter\StringTrim::class]
            ],
        ];

        if (!empty($this->max)) {
            $validators = [
                'name' => StringLength::class,
                'options' => [
                    'min' => 5,
                    'max' => $this->max,
                ]
            ];

            if (!empty($this->getOptions()['minLength_validation_error_message'])) {
                $validators['options']['messages'][StringLength::TOO_SHORT] =
                    $this->getOptions()['minLength_validation_error_message'];
            }
            if (!empty($this->getOptions()['maxLength_validation_error_message'])) {
                $validators['options']['messages'][StringLength::TOO_SHORT] =
                    $this->getOptions()['maxLength_validation_error_message'];
            }

            $specification['validators'][] = $validators;
        }

        if (!$this->allowEmpty && !empty($this->getOptions()['notEmpty_validation_error_message'])) {
            $specification['validators'][] = [
                'name' => NotEmpty::class,
                'options' => [
                    'messages' => [
                        NotEmpty::IS_EMPTY =>
                            $this->getOptions()['notEmpty_validation_error_message']
                    ],
                ]
            ];
        }

        return $specification;
    }
}
