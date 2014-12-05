<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("vehicle-filter")
 * @Form\Attributes({"method":"get","class":"form__filter"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true})
 */
class VehicleFilter
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
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"id":"specified","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-specified",
     *     "value_options": {"A":"All", "Y":"Yes", "N":"No"},
     *     "disable_inarray_validator": false
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $specified = null;

    /**
     * @Form\Attributes({"id":"includeRemoved","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-include-removed",
     *     "value_options": {
     *
     *     },
     *     "must_be_checked": true
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\Checkbox")
     */
    public $includeRemoved = null;

    /**
     * @Form\Attributes({"id":"disc","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-disc",
     *     "value_options": {"A":"All", "Y":"Yes", "N":"No"},
     *     "disable_inarray_validator": false
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $disc = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary"})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-filter",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-10"
     * })
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $filter = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $sort = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $order = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $limit = null;
}
