<?php

namespace Common\Form\Model\Form\Lva;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lva-community-licence-filter")
 * @Form\Attributes({"method":"get", "class":"form__filter"})
 * @Form\Type("Common\Form\Form")
 */
class CommunityLicenceFilter
{
    /**
     * @Form\Options({
     *     "category": "com_lic_sts"
     * })
     * @Form\Type("\Common\Form\Element\DynamicMultiCheckbox")
     */
    public $status = null;

    /**
     * @Form\Attributes({"value":1})
     * @Form\Type("Hidden")
     */
    public $isFiltered = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary","data-container-class":"js-hidden"})
     * @Form\Options({
     *     "label": "lva-community-licence-filter-button"
     * })
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $filter = null;
}
