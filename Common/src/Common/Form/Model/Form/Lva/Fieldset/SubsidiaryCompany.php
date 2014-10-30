<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;
use Common\Form\Model\Form\Traits\VersionTrait;

/**
 * Subsidiary company
 */
class SubsidiaryCompany
{
    use VersionTrait;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"application_your-business_business-details-formName"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $name = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"application_your-business_business-details-formCompanyNo"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":8,"max":8}})
     * @Form\Validator({
     *     "name": "Alnum",
     *     "options": {
     *         "messages": {
     *             "notAlnum": "Must be 8 digits; alpha-numeric characters allowed; no
     * special characters; mandatory when displayed"
     *         }
     *     }
     * })
     */
    public $companyNo = null;
}
