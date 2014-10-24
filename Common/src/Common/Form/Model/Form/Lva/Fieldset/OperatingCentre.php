<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 */
class OperatingCentre
{

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.data.noOfVehiclesPossessed"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\NumberOfVehicles")
     */
    public $noOfVehiclesPossessed = null;

    /**
     * @Form\Options({
     *     "label":
     * "application_operating-centres_authorisation-sub-action.data.noOfTrailersPossessed"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\NumberOfVehicles")
     */
    public $noOfTrailersPossessed = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label":
     * "application_operating-centres_authorisation-sub-action.data.sufficientParking",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $sufficientParking = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label":
     * "application_operating-centres_authorisation-sub-action.data.permission",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $permission = null;
}
