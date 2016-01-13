<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("disc-filter")
 * @Form\Attributes({"method":"get","class":"form__filter"})
 * @Form\Type("Common\Form\Form")
 * @Form\Options({"prefer_form_input_filter": true})
 */
class DiscFilter
{
    /**
     * @Form\Attributes({"id":"includeCeased","placeholder":""})
     * @Form\Options({
     *     "label": "internal-vehicle-filter-include-ceased-discs",
     *     "value_options": {
     *
     *     },
     *     "must_be_checked": true
     * })
     * @Form\Type("OlcsCheckbox")
     */
    public $includeCeased = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary","data-container-class":"js-hidden"})
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
}
