<?php

/**
 * Email Filter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as ZendElement;
use Laminas\Validator as LaminasValidator;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Email Filter
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Email extends ZendElement implements InputProviderInterface
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
            'filters' => [
                ['name' => 'Laminas\Filter\StringTrim'],
            ],
            'validators' => [
                // @NOTE don't know if this is still used but I'll update it anyway
                ['name' => 'Dvsa\Olcs\Transfer\Validators\EmailAddress'],
                ['name' => 'Laminas\Validator\StringLength', 'options'=> ['min' => 5, 'max' => 255]],
            ]
        ];

        return $specification;
    }
}
