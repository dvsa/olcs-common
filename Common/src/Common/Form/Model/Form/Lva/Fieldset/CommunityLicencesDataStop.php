<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("community-licences-data-stop")
 */
class CommunityLicencesDataStop
{
    /**
     * @Form\Annotations({"id":""})
     * @Form\Options({
     *     "label": "internal.community_licence.form.change_status_to",
     *     "value_options": {
     *          "Y": "internal.community_licence.form.suspended",
     *          "N": "internal.community_licence.form.withdrawn"
     *      },
     * })
     * @Form\Type("radio")
     */
    public $type = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","class":"chosen-select-medium",  "multiple" : true})
     * @Form\Options({
     *     "label": "internal.community_licence.form.reasons",
     *     "category":"com_lic_sw_reason",
     * })
     * @Form\Type("DynamicSelect")
     * @Form\Validator({
     *      "name": "Zend\Validator\NotEmpty",
     *      "options": {
     *          "messages":{Zend\Validator\NotEmpty::IS_EMPTY:"internal.community_licence.form.licences_validation"}
     *      }
     * })
     */
    public $reason = null;
}
