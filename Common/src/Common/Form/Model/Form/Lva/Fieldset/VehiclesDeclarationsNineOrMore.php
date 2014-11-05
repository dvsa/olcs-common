<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("nineOrMore")
 */
class VehiclesDeclarationsNineOrMore
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
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvNoSmallVhlConfirmation = null;
}
