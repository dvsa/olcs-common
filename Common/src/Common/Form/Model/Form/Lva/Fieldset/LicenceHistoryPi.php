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
     *     "label":"application_previous-history_licence-history_prevBeenAtPi",
     *     "error-message":"licenceHistoryPi_prevBeenAtPi-error",
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
     *     "options": {"table": "prevBeenAtPi-table"}
     *})
     */
    public $prevBeenAtPi = null;

    /**
     * @Form\Name("prevBeenAtPi-table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     * @Form\Attributes({"id":"prevBeenAtPi"})
     */
    public $prevBeenAtPiTable = null;
}
