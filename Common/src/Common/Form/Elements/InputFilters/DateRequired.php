<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Laminas\Form\Element\DateSelect as LaminasDateSelect;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;
use Laminas\Validator\Date as DateValidator;

/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */
class DateRequired extends LaminasDateSelect implements InputProviderInterface
{
    protected $required = true;

    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $specification = [
            'name' => $this->getName(),
            'required' => $this->required,
            'filters' => [['name' => 'DateSelectNullifier']
            ],
            'validators' => $this->getValidators()
        ];

        return $specification;
    }

    public function getValidators()
    {
        return [
            ['name' => 'Date', 'options'=>['format' => 'Y-m-d']]
        ];
    }
}
