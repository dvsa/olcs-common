<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 */
class ApplicationPreviousHistoryLicenceHistorySubActionData
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
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $previousLicenceType = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({"label":"selfserve-app-subSection-previous-history-licence-history-licNo"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $licNo = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-licence-history-holderName"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $holderName = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-licence-history-willSurrender",
     *     "value_options": "yes_no",
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $willSurrender = null;

    /**
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-licence-history-disqualificationDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Required(false)
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $disqualificationDate = null;

    /**
     * @Form\Attributes({"class":"long","id":""})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-licence-history-disqualificationLength"
     * })
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $disqualificationLength = null;

    /**
     * @Form\Attributes({"id":"dob"})
     * @Form\Options({
     *     "label":
     * "selfserve-app-subSection-previous-history-licence-history-purchaseDate",
     *     "create_empty_option": true,
     *     "render_delimiters": false
     * })
     * @Form\Required(false)
     * @Form\Type("DateSelect")
     * @Form\Filter({"name":"DateSelectNullifier"})
     * @Form\Validator({"name":"Date","options":{"format":"Y-m-d"}})
     * @Form\Validator({"name":"\Common\Form\Elements\Validators\DateNotInFuture"})
     */
    public $purchaseDate = null;


}

