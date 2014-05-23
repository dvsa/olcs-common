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
use Common\Form\Elements\Validators\Time as TimeValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Checks if the hearing time is entered then the date is also entered
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
                new \Common\Form\Elements\Validators\TimeWithDate('hearingDate'),
                new TimeValidator(array("format" => 'H:i'))
            ]
        ];

        return $specification;
    }
}
