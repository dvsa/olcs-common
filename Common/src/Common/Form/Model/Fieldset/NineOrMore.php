<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("nineOrMore")
 */
class NineOrMore
{

    /**
     * @Form\Options({"label":"application_vehicle-safety_undertakings.nineOrMore.label"})
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvNoSmallVhlConfirmationLabel = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.nineOrMore.details",
     *     "value_options": {
     *
     *     },
     *     "help-block": "Please choose",
     *     "must_be_value": "1"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvNoSmallVhlConfirmation = null;


}

