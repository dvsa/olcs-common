<?php
/**
 *
 * @author Jakub.Igla <jakub.igla@valtech.co.uk
 *
 */

namespace Common\Form\Elements\InputFilters;
use Zend\Form\Element\DateSelect as ZendDateSelect;
use Zend\Validator as ZendValidator;
use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

class OffenceDateBeforeConvictionDate extends ZendDateSelect implements InputProviderInterface
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
                new \Common\Form\Elements\Validators\StringLessThan('dateOfConviction'),
            ]
        ];

        return $specification;
    }
}