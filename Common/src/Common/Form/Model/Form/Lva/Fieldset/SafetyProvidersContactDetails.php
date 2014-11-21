<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-safety-providers-contact-details")
 * @Form\Options({})
 */
class SafetyProvidersContactDetails
{
    /**
     * @Form\Attributes({"class":"medium","id":""})
     * @Form\Options({"label":"application_vehicle-safety_safety-sub-action.data.fao"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $fao = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;
}
