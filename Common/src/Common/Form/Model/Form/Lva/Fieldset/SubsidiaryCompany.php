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
     * @Form\Options({
     *     "label":"application_your-business_business-details-formName",
     *     "error-message": "subsidiary-company-name-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $name = null;

    /**
     * @Form\Attributes({
     *     "class":"long",
     *     "id":"",
     *     "pattern":"\d*"
     * })
     * @Form\Options({
     *     "label":"application_your-business_business-details-formCompanyNo",
     *     "error-message": "subsidiary-company-number-error"
     * })
     * @Form\Type("Common\Form\Elements\InputFilters\CompanyNumber")
     */
    public $companyNo = null;
}
