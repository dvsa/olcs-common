<?php

/**
 * VehicleNumber validation
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element as ZendElement;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\VehiclesNumber as VehiclesNumberValidator;

/**
 * VehicleNumber validation
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class VehiclesNumber extends ZendElement implements InputProviderInterface
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
            'validators' => [
                new ZendValidator\Digits(),
                new VehiclesNumberValidator($this->getName())
            ]
        ];

        return $specification;
    }
}
