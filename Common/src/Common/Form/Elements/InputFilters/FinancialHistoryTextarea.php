<?php

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\Textarea as LaminasElement;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

/**
 * Input Specification for Finacial History additional info
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class FinancialHistoryTextarea extends LaminasElement implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     */
    public function getInputSpecification(): array
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [
                new Validator\NotEmpty(Validator\NotEmpty::NULL),
                new \Dvsa\Olcs\Transfer\Validators\FhAdditionalInfo(),
            ]
        ];
    }
}
