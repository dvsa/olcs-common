<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("dataLicencesRevoked")
 */
class LicenceHistoryDataLicencesRevoked
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevBeenRevoked",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\PreviousHistoryLicenceHistoryNeedLicence")
     */
    public $prevBeenRevoked = null;
}
