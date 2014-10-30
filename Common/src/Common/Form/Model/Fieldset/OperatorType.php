<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("operator-type")
 * @Form\Attributes({"class":"hidden"})
 * @Form\Options({"label":"application_type-of-licence_operator-type.data"})
 */
class OperatorType
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
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_type-of-licence_operator-type.data.goodsOrPsv",
     *     "help-block": "Please choose",
     *     "service_name": "staticList",
     *     "category": "operator_types"
     * })
     * @Form\Type("DynamicRadio")
     */
    public $goodsOrPsv = null;
}
