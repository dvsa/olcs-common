<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

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
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--advanced"},
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvNoSmallVhlConfirmation = null;
}
