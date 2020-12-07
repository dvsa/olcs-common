<?php

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\Textarea as ZendElement;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

/**
 * Input Specification for Finacial History additional info
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class FinancialHistoryTextarea extends ZendElement implements InputProviderInterface
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
                new Validator\NotEmpty(Validator\NotEmpty::NULL),
                new \Dvsa\Olcs\Transfer\Validators\FhAdditionalInfo(),
            ]
        ];

        return $specification;
    }
}
