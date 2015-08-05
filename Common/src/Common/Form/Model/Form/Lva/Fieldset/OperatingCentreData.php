<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Operating centre fieldset
 */
class OperatingCentreData
{
    /**
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfVehiclesRequired"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfVehiclesRequired = null;

    /**
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfTrailersRequired"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfTrailersRequired = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label": "application_operating-centres_authorisation-sub-action.data.sufficientParking",
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
     *     "label": "application_operating-centres_authorisation-sub-action.data.permission",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $permission = null;
}
