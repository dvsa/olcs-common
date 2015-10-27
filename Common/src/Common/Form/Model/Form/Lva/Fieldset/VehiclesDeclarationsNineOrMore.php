<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("nineOrMore")
 * @Form\Attributes({
 *     "class": "psv-show-large"
 * })
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
     * @Form\Attributes({"data-container-class": "confirm"})
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvNoSmallVhlConfirmation = null;
}
