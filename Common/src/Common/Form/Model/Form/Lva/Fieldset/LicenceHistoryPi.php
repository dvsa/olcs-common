<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Licence History Pi
 */
class LicenceHistoryPi
{
    /**
     * @Form\Attributes({"id":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevBeenAtPi",
     *     "value_options": {
     *         {
     *             "value": "Y",
     *             "label": "Yes",
     *             "label_attributes": {
     *                 "aria-label": "Has anyone you've named ever taken part in a public inquiry held by a Traffic Commissioner? Yes"
     *             }
     *         },
     *         {
     *             "value": "N",
     *             "label": "No"
     *         }
     *     },
     *     "fieldset-attributes" : {
     *          "class":"checkbox inline"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator",
     *     "options": {"table": "prevBeenAtPi-table"}
     *})
     */
    public $prevBeenAtPi = null;

    /**
     * @Form\Name("prevBeenAtPi-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $prevBeenAtPiTable = null;
}
