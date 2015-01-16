<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":"fieldset--secondary"})
 */
class LicenceHistoryApplied
{
    /**
     * @Form\Options({"label":"application_previous-history_licence-history_personsInformation"})
     * @Form\Type("Common\Form\Elements\Types\PlainText")
     */
    public $personsInformation = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevHadLicence",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "fieldset-attributes" : {
     *          "class":"subquestion"
     *     }
     * })
     * @Form\Type("radio")
     * @Form\Validator({"name":"Common\Form\Elements\Validators\LicenceHistoryLicenceValidator"})
     */
    public $question = null;

    /**
     * @Form\Name("table")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Table")
     */
    public $table = null;
}
