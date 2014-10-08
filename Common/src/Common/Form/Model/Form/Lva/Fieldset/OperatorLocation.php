<?php

/**
 * Operator Location fieldset
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;
use Common\Form\Model\Form\Fieldset\Base;

/**
 * @Form\Name("operator-location")
 */
class OperatorLocation extends Base
{
    /**
    * @Form\Name("niFlag")
    * @Form\Options({
    *      "label": "application_type-of-licence_operator-location.data.niFlag",
    *      "value_options":{
    *          "N":"Great Britain",
    *          "Y":"Northern Ireland"
    *      }
    * })
    * @Form\Type("Radio")
    */
    public $niFlag = null;
}
