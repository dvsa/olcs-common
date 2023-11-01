<?php

/**
 * Gpw Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element as LaminasElement;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;
use Laminas\Validator\Digits;
use Laminas\Validator\GreaterThan;
use Laminas\I18n\Validator\Alnum;

/**
 * Gpw Element
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class Gpw extends LaminasElement implements InputProviderInterface
{

    public function __construct($name = null, $options = array())
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
            'validators' => [
                ['name' => 'Laminas\Validator\Digits'],
                ['name' => 'Laminas\Validator\GreaterThan', 'options' =>['min' => 0]],
            ]
        ];

        return $specification;
    }
}
