<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Operating centre fieldset
 */
class OperatingCentreData
{
    /**
     * @Form\Attributes({"pattern":"\d*"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfVehiclesRequired",
     *     "error-message": "Your total number of vehicles"
     * })
     * @Form\Validator({"name": "Between", "options": {"min":0, "max":1000000}})
     */
    public $noOfVehiclesRequired = null;

    /**
     * @Form\Attributes({"pattern":"\d*"})
     * @Form\Options({
     *     "label": "application_operating-centres_authorisation-sub-action.data.noOfTrailersRequired",
     *     "error-message": "Your total number of trailers"
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
