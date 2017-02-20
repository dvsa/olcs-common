<?php

namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;
use Zend\InputFilter\InputProviderInterface;

/**
 * Vrm field for UK vehicles
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
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
                new \Common\Filter\Vrm(),
            ],
            'validators' => [
                new \Dvsa\Olcs\Transfer\Validators\Vrm(),
            ],
        ];

        return $specification;
    }
}
