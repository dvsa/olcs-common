<?php

namespace Common\Form\Model\Form\Lva;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("psv-vehicle-filter")
 * @Form\Attributes({"method":"get","class":"filters form__filter"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true})
 */
class PsvVehicleFilter
{
    /**
     * @Form\Attributes({"id":"vrm","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-vrm",
     *     "value_options": {
     *
     *     },
     *     "disable_inarray_validator": false
     * })
     * @Form\Type("\Laminas\Form\Element\Select")
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"id":"specified","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-specified",
     *     "value_options": {"A":"All", "Y":"Yes", "N":"No"},
     *     "disable_inarray_validator": false
     * })
     * @Form\Type("\Laminas\Form\Element\Select")
     */
    public $specified = null;

    /**
     * @Form\Attributes({"id":"disc","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-disc",
     *     "value_options": {"A":"All", "Y":"Yes", "N":"No"},
     *     "disable_inarray_validator": false
     * })
     * @Form\Type("\Laminas\Form\Element\Select")
     */
    public $disc = null;

    /**
     * @Form\Attributes({"id":"includeRemoved","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-include-removed",
     *     "value_options": {
     *
     *     },
     *     "must_be_checked": true
     * })
     * @Form\Type("OlcsCheckbox")
     */
    public $includeRemoved = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary","data-container-class":"js-hidden"})
     * @Form\Options({"label": "internal-vehicle-filter-filter"})
     * @Form\Type("\Laminas\Form\Element\Button")
     */
    public $filter = null;

    /**
     * @Form\Type("Hidden")
     */
    public $limit = null;
}
