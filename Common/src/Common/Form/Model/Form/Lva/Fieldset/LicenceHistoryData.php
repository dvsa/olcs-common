<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Licence History Data
 */
class LicenceHistoryData
{
    /**
     * @Form\Attributes({"value": "markup-application_previous-history_licence-history_data"})
     * @Form\Type("Common\Form\Elements\Types\HtmlTranslated")
     */
    public $title;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevHasLicence",
     *     "error-message":"licenceHistoryData_prevHasLicence-error",
     *     "value_options":{
     *         {
     *             "value":"Y",
     *             "label":"Yes",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         },{
     *             "value":"N",
     *             "label":"No",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         }
     *     },
     *     "fieldset-attributes":{
     *          "class":"checkbox"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevHasLicence-table"}
     *})
     */
    public $prevHasLicence = null;

    /**
     * @Form\Name("prevHasLicence-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevHasLicence"})
     */
    public $prevHasLicenceTable = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevHadLicence",
     *     "error-message": "licenceHistoryData_prevHadLicence-error",
     *     "value_options": {
     *         {
     *             "value":"Y",
     *             "label":"Yes",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         },{
     *             "value":"N",
     *             "label":"No",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         }
     *     },
     *     "fieldset-attributes":{
     *          "class":"checkbox"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevHadLicence-table"}
     *})
     */
    public $prevHadLicence = null;

    /**
     * @Form\Name("prevHadLicence-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevHadLicence"})
     */
    public $prevHadLicenceTable = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevBeenDisqualifiedTc",
     *     "error-message":"licenceHistoryData_prevBeenDisqualifiedTc-error",
     *     "value_options":{
     *         {
     *             "value":"Y",
     *             "label":"Yes",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         },{
     *             "value":"N",
     *             "label":"No",
     *             "label_attributes":{
     *                 "class":"inline"
     *             }
     *         }
     *     },
     *     "fieldset-attributes":{
     *          "class":"checkbox"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevBeenDisqualifiedTc-table"}
     *})
     */
    public $prevBeenDisqualifiedTc = null;

    /**
     * @Form\Name("prevBeenDisqualifiedTc-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevBeenDisqualifiedTc"})
     */
    public $prevBeenDisqualifiedTcTable = null;
}
