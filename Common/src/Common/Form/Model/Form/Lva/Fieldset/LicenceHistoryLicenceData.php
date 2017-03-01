<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class LicenceHistoryLicenceData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $previousLicenceType = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":"selfserve-app-subSection-previous-history-licence-history-licNo",
     *     "error-message": "licenceHistoryLicenceData_licNo-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $licNo = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-licence-history-holderName",
     *     "error-message": "licenceHistoryLicenceData_holderName-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $holderName = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-licence-history-willSurrender",
     *     "error-message": "licenceHistoryLicenceData_willSurrender-error",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $willSurrender = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"dob","required":false})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-licence-history-disqualificationDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $disqualificationDate = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-licence-history-disqualificationLength"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $disqualificationLength = null;

    /**
     * @Form\Required(true)
     * @Form\Attributes({"id":"dob","required":false})
     * @Form\Options({
     *     "label": "selfserve-app-subSection-previous-history-licence-history-purchaseDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name": "\Common\Validator\Date"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $purchaseDate = null;
}
