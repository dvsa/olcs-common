<?php

/**
 * Operator type fieldset
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("operator-type")
 */
class OperatorType
{
    /**
    * @Form\Name("goodsOrPsv")
    * @Form\Options({
    *      "label": "application_type-of-licence_operator-type.data.goodsOrPsv",
    *      "value_options":{
    *          "lcat_gv":"Goods",
    *          "lcat_psv":"PSV"
    *      }
    * })
    * @Form\Type("Radio")
    */
    public $goodsOrPsv = null;
}
