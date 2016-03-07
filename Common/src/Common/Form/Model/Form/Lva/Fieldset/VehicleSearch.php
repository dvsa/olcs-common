<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Vehicle Search
 */
class VehicleSearch
{
    /**
     * @Form\Attributes({"id":"vrm","placeholder":"","class":"inline-search__input"})
     * @Form\Options({
     *     "label": "vehicle-search-vrm",
     * })
     * @Form\Type("\Zend\Form\Element\Text")
     * @Form\Required(true)
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary inline-search__button"})
     * @Form\Options({
     *     "label": "vehicle-search-search",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-10"
     * })
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $filter = null;

    /**
     * @Form\Attributes({"type":"submit","class":"inline-search__hint","id":"clearSearch"})
     * @Form\Options({
     *     "label": "vehicle-search-clear-search"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $clearSearch = null;
}
