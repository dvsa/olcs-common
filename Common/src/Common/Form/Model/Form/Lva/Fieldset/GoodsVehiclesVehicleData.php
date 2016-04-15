<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class GoodsVehiclesVehicleData
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
     * @Form\Attributes({"class":"medium","id":"vrm","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.vrm",
     *     "label_attributes": {
     *         "aria-label": "Enter your vehicle registration number"
     *     }
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Common\Filter\Vrm"})
     * @Form\Validator({"name":"Dvsa\Olcs\Transfer\Validators\Vrm"})
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"class":"small","id":"plated_weight","placeholder":"","pattern":"\d(\.)*"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.weight",
     *     "label_attributes": {
     *         "aria-label": "Enter the gross plated weight in kilograms"
     *     }
     * })
     * @Form\Validator({"name": "Zend\Validator\Digits"})
     * @Form\Validator({"name": "Zend\Validator\Between", "options": {"min": 0, "max": 999999}})
     * @Form\Type("Text")
     */
    public $platedWeight = null;
}
