<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 * @Form\Options({"label":"application_type-of-licence_operator-type.data"})
 */
class ApplicationTypeOfLicenceOperatorTypeData
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
     *     "category": "operator_types"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $goodsOrPsv = null;


}

