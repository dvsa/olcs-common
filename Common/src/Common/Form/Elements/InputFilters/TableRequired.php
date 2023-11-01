<?php

namespace Common\Form\Elements\InputFilters;

use Common\Form\Elements\Types\Table;
use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\TableRequiredValidator;
use Common\Service\Table\TableBuilder;

/**
 * Table Requried
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @deprecated Not used anythere and must be removed as part of https://jira.i-env.net/browse/OLCS-15198
 * It seems like the only validator being used is Common/Form/Elements/Validators/TableRequiredValidator.php
 */
class TableRequired extends Table implements InputProviderInterface
{
    /**
     * Provide default input rules for this element.
     *
     * @return array
     */
    public function getInputSpecification(): array
    {
        $label = 'row to the table';

        $table = $this->getTable();

        if ($table instanceof TableBuilder) {
            $label = $table->getVariable('required_label');
        }

        $specification = [
            'name' => $this->getName(),
            'required' => true,
            'continue_if_empty' => true,
            'allow_empty' => false,
            'filters' => [

            ],
            'validators' => array(
                new TableRequiredValidator(array('label' => $label))
            )
        ];

        return $specification;
    }
}
