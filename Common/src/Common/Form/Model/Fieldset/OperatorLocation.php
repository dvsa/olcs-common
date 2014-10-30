<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("operator-location")
 * @Form\Attributes({"class":"hidden"})
 * @Form\Options({"label":"application_type-of-licence_operator-location.data"})
 */
class OperatorLocation
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
     *     "label": "application_type-of-licence_operator-location.data.niFlag",
     *     "help-block": "Please choose",
     *     "value_options": {"Y":"Northern Ireland", "N":"Great Britain"}
     * })
     * @Form\Type("Radio")
     */
    public $niFlag = null;
}
