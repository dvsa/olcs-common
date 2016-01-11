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
     *     "label": "application_previous-history_licence-history_prevBeenRefused",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "fieldset-attributes" : {
     *          "class":"checkbox inline"
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
     */
    public $prevBeenRefusedTable = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevBeenRevoked",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "fieldset-attributes" : {
     *          "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevBeenRevoked-table"}
     *})
     */
    public $prevBeenRevoked = null;

    /**
     * @Form\Name("prevBeenRevoked-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $prevBeenRevokedTable = null;
}
