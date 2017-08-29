<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("licence-vehicle")
 */
class LicenceVehicle
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
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.licence-vehicle.receivedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "default_date": "now"
     * })
     * @Form\Required(false)
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Type("Common\Form\Elements\Custom\DateSelect")
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     */
    public $receivedDate = null;

    /**
     * @Form\Required(false)
     * @Form\Type("DateTimeSelect")
     * @Form\Attributes({"id":"specifiedDate"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.licence-vehicle.specifiedDate",
     *     "create_empty_option": false,
     *     "render_delimiters": true,
     *     "pattern": "d MMMM y '</fieldset><fieldset><div class=""field""><label for=""hearingDate"">Specified time</label>'HH:mm:ss'</div>'",
     *     "field": "specifiedDate",
     *     "month_attributes": {"disabled":"disabled"},
     *     "year_attributes": {"disabled":"disabled"},
     *     "day_attributes": {"disabled":"disabled"},
     *     "hour_attributes": {"disabled":"disabled"},
     *     "minute_attributes": {"disabled":"disabled"},
     *     "default_date": "now",
     *     "display_every_minute": true
     * })
     * @Form\Filter({"name": "DateTimeSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name": "Date", "options": {"format": "Y-m-d H:i:s"}})
     */
    public $specifiedDate;

    /**
     * @Form\Attributes({})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.licence-vehicle.removalDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false,
     *     "month_attributes": {"disabled":"disabled"},
     *     "year_attributes": {"disabled":"disabled"},
     *     "day_attributes": {"disabled":"disabled"}
     * })
     * @Form\Required(false)
     * @Form\Filter({"name": "DateSelectNullifier"})
     * @Form\Type("Common\Form\Elements\Custom\DateSelect")
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     */
    public $removalDate = null;

    /**
     * @Form\Attributes({"disabled":"disabled"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.licence-vehicle.discNo"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $discNo = null;
}
