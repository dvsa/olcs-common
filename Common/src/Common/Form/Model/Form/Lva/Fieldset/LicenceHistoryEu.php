<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * Licence History Eu
 */
class LicenceHistoryEu
{
    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevBeenRefused",
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "legend-attributes": {"class": "form-element__label"},
     *     "error-message":"licenceHistoryEu_prevBeenRefused-error",
     *     "value_options": {"Y":"Yes", "N":"No"}
     * })
     * @Form\Type("radio")
     * @Form\Validator("Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     options={"table": "prevBeenRefused-table"}
     *)
     * @Form\Flags({"priority": -10})
     */
    public $prevBeenRefused = null;

    /**
     * @Form\Name("prevBeenRefused-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({
     *      "id":"prevBeenRefused",
     *      "class": "help__text help__text--removePadding"
     * })
     * @Form\Flags({"priority": -20})
     */
    public $prevBeenRefusedTable = null;

    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label":"application_previous-history_licence-history_prevBeenRevoked",
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "legend-attributes": {"class": "form-element__label"},
     *     "error-message":"licenceHistoryEu_prevBeenRevoked-error",
     *     "value_options": {"Y":"Yes", "N":"No"}
     * })
     * @Form\Type("radio")
     * @Form\Validator("Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     options={"table":"prevBeenRevoked-table"}
     *)
     * @Form\Flags({"priority": -30})
     */
    public $prevBeenRevoked = null;

    /**
     * @Form\Name("prevBeenRevoked-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({
     *      "id":"prevBeenRevoked",
     *      "class": "help__text help__text--removePadding"
     * })
     * @Form\Flags({"priority": -40})
     */
    public $prevBeenRevokedTable = null;
}
