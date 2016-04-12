<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class GoodsVehiclesEditVehicleData
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
     * @Form\Attributes({"class":"medium","id":"vrm","placeholder":"","disabled":"disabled"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.vrm"
     * })
     * @Form\Type("Text")
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"class":"small","id":"plated_weight","placeholder":"","pattern":"\d*"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.weight",
     *     "label_attributes": {
     *         "aria-label": "Enter the gross plated weight in kilograms"
     *     }
     * })
     * @Form\Validator({"name": "Zend\Validator\Between", "options": {"min": 0, "max": 999999}})
     * @Form\Type("Text")
     */
    public $platedWeight = null;
}
