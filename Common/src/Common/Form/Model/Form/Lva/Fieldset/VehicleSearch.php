<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * Vehicle Search
 * @Form\Options({
 *     "label": "vehicle-search-vrm",
 * })
 */
class VehicleSearch
{
    /**
     * @Form\Attributes({"id":"vrm","placeholder":"","class":"inline-search__input"})
     * @Form\Type("\Laminas\Form\Element\Text")
     * @Form\Required(true)
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary inline-search__submit"})
     * @Form\Options({
     *     "label": "vehicle-search-search",
     * })
     * @Form\Type("\Laminas\Form\Element\Button")
     */
    public $filter = null;

    /**
     * @Form\Attributes({"type":"submit","class":"inline-search__clear","id":"clearSearch"})
     * @Form\Options({
     *     "label": "vehicle-search-clear-search"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $clearSearch = null;
}
