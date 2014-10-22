<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("dataLicencesDisqualified")
 */
class LicenceHistoryDataLicencesDisqualified
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_previous-history_licence-history_prevBeenDisqualifiedTc",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\PreviousHistoryLicenceHistoryNeedLicence")
     */
    public $prevBeenDisqualifiedTc = null;
}
