<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("dataLicencesPublicInquiry")
 */
class DataLicencesPublicInquiry
{

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevBeenAtPi",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\PreviousHistoryLicenceHistoryNeedLicence")
     */
    public $prevBeenAtPi = null;


}

