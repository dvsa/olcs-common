<?php

namespace Common\Form\Model\Fieldset;

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
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_vehicle-psv-sub-action.licence-vehicle.receivedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Required(false)
     * @Form\Type("Common\Form\Elements\Custom\DateSelect")
     */
    public $receivedDate = null;

    /**
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_vehicle-psv-sub-action.licence-vehicle.specifiedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Required(false)
     * @Form\Type("Common\Form\Elements\Custom\DateSelect")
     */
    public $specifiedDate = null;

    /**
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_vehicle-psv-sub-action.licence-vehicle.deletedDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Required(false)
     * @Form\Type("Common\Form\Elements\Custom\DateSelect")
     */
    public $deletedDate = null;

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_vehicle-psv-sub-action.licence-vehicle.discNo"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $discNo = null;
}
