<?php

/**
 * VehicleSafetyTachographAnalyserContractor
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehicleSafetyTachographAnalyserContractorValidator;

/**
 * VehicleSafetyTachographAnalyserContractor
 */
class VehicleSafetyTachographAnalyserContractor extends Text implements InputProviderInterface
{
    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new VehicleSafetyTachographAnalyserContractorValidator()
        );
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
            'continue_if_empty' => true,
            'allow_empty' => false,
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
