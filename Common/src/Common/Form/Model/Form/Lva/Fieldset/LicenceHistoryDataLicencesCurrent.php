<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("dataLicencesCurrent")
 * @Form\Options({"label":"application_previous-history_licence-history.title"})
 */
class LicenceHistoryDataLicencesCurrent
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_previous-history_licence-history_prevHasLicence",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\PreviousHistoryLicenceHistoryNeedLicence")
     */
    public $prevHasLicence = null;
}
