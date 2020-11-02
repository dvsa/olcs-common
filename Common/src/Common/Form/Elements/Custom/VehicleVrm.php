<?php

namespace Common\Form\Elements\Custom;

use Common\Filter\Vrm;
use Dvsa\Olcs\Transfer\Validators\Vrm as VrmValidator;
use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\NotEmpty;

/**
 * Vrm field for UK vehicles
 */
class VehicleVrm extends ZendElement implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'filters' => [
                new Vrm(),
            ],
            'validators' => $this->getValidators(),
        ];

        return $specification;
    }

    protected function getValidators()
    {
        $validators = [
            [
                'name' => NotEmpty::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'messages' => [
                        NotEmpty::IS_EMPTY => 'licence.vehicle.add.search.vrm-missing'
                    ]
                ],
            ]
        ];

        if ($this->getOption('validateVrm') ?? true) {
            $validators[] = [
                'name' => VrmValidator::class
            ];
        }

        return $validators;
    }
}
