<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Licence History Eu
 */
class LicenceHistoryEu
{
    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevBeenRefused",
     *     "error-message":"licenceHistoryEu_prevBeenRefused-error",
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
     *     "options": {"table": "prevBeenRefused-table"}
     *})
     */
    public $prevBeenRefused = null;

    /**
     * @Form\Name("prevBeenRefused-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevBeenRefused"})
     */
    public $prevBeenRefusedTable = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevBeenRevoked",
     *     "error-message":"licenceHistoryEu_prevBeenRevoked-error",
     *     "value_options":{
     *         {
     *             "value":"Y",
     *             "label":"Yes",
     *             "label_attributes": {
     *                 "aria-label":"application_previous-history_licence-history_prevBeenRevoked",
     *                 "class": "inline"
     *             }
     *         },{
     *             "value": "N",
     *             "label": "No",
     *             "label_attributes": {
     *                 "class": "inline"
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
     *     "options":{"table":"prevBeenRevoked-table"}
     *})
     */
    public $prevBeenRevoked = null;

    /**
     * @Form\Name("prevBeenRevoked-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevBeenRevoked"})
     */
    public $prevBeenRevokedTable = null;
}
