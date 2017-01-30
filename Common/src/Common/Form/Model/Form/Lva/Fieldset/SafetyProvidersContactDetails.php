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
     * @Form\Type("Text")
     * @Form\Attributes({"class":"medium","id":""})
     * @Form\Options({
     *     "label":"application_vehicle-safety_safety-sub-action.data.fao",
     *     "label_attributes": {"aria-label":"application_vehicle-safety_safety-sub-action.data.fao"}
     * })
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength",
     *     "options":{"min":1,"max":90}
     * })
     * @Form\Validator({"name":"regex", 
     *     "options":{"pattern":"/^[a-z ,.'-]+$/i","messages":{"regexNotMatch":"error.characters.not-allowed"}}
     * })
     */
    public $fao = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;
}
