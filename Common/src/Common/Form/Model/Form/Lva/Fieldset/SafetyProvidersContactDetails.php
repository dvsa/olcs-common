<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("contactDetails")
 * @Form\Options({})
 */
class ApplicationVehicleSafetySafetySubActionContactDetails
{

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({"label":"application_vehicle-safety_safety-sub-action.data.fao"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $fao = null;

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


}

