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
     *     "help-block": "Between 2 and 50 characters."
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Common\Filter\Vrm"})
     * @Form\Validator({"name":"Common\Form\Elements\Validators\Vrm"})
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"class":"small","id":"plated_weight","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.weight",
     *     "help-block": "Between 2 and 50 characters."
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\VehicleWeight")
     */
    public $platedWeight = null;
}
