<?php
/**
 * Input Specification for Conviction offence details
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\Textarea as LaminasElement;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Input Specification for Convition additional info
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class ConvictionTextarea extends LaminasElement implements InputProviderInterface
{

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'allow_empty' => false,
            'validators' => [
            ]
        ];

        return $specification;
    }
}
