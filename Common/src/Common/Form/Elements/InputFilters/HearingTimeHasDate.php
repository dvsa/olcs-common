<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Zend\Form\Element\Time as ZendTimeSelect;
use Zend\Validator as ZendValidator;
use Zend\Validator\Date as DateValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Checks conviction offence date is before the conviction date
 */
class HearingTimeHasDate extends ZendTimeSelect implements InputProviderInterface
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
            'required' => false,

            'validators' => [
                new DateValidator(array("format" => 'H:i')),
                new \Common\Form\Elements\Validators\TimeWithDate('hearingDate'),

            ]
        ];

        return $specification;
    }
}
