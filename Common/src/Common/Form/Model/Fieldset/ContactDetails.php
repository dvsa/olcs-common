<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("contactDetails")
 * @Form\Options({"label":"application_taxi-phv_licence-sub-action.contactDetails"})
 */
class ContactDetails
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
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({
     *     "label":
     * "application_taxi-phv_licence-sub-action.contactDetails.description"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $description = null;


}

