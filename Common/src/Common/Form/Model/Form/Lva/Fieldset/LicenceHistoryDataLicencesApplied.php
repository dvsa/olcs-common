<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("dataLicencesApplied")
 */
class LicenceHistoryDataLicencesApplied
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
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\PreviousHistoryLicenceHistoryNeedLicence")
     */
    public $prevHadLicence = null;
}
