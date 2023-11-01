<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\Time as LaminasTimeSelect;
use Laminas\Validator as LaminasValidator;
use Laminas\Validator\Date as DateValidator;
use Common\Form\Elements\Validators\Time as TimeValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Checks if the hearing time is entered then the date is also entered
 */
class HearingTimeHasDate extends LaminasTimeSelect implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
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
