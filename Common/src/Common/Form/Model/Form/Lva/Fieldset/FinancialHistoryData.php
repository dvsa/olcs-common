<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("data")
 * @Form\Options({
 *     "label": "application_previous-history_financial-history.finance.title",
 *     "hint": "application_previous-history_financial-history.finance.hint"
 * })
 */
class FinancialHistoryData
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
     * @Form\Options({"label":"application_previous-history_financial-history.finance.hint"})
     * @Form\Type("Common\Form\Elements\Types\PlainText")
     */
    public $hasAnyPerson = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_financial-history.finance.bankrupt",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $bankrupt = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.liquidation",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $liquidation = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.receivership",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $receivership = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.administration",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $administration = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.finance.disqualified",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $disqualified = null;

    /**
     * @Form\Required(true)
     * @Form\AllowEmpty(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Attributes({
     *     "required": false,
     *     "id": "",
     *     "class": "long   js-financial-history",
     *     "placeholder":
     * "application_previous-history_financial-history.insolvencyDetails.placeholder",
     *     "data-container-class": "highlight-box"
     * })
     * @Form\Options({
     *     "label":
     * "application_previous-history_financial-history.insolvencyDetails.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box.",
     *     "hint":
     * "application_previous-history_financial-history.insolvencyDetails.hint"
     * })
     * @Form\Type("TextArea")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name": "Common\Form\Elements\Validators\FHAdditionalInfo"})
     */
    public $insolvencyDetails = null;

    /**
     * @Form\Attributes({"id":"file"})
     * @Form\Type("\Common\Form\Elements\Types\MultipleFileUpload")
     */
    public $file = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "label":
     * "application_previous-history_financial-history.insolvencyConfirmation.title",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $insolvencyConfirmation = null;
}
