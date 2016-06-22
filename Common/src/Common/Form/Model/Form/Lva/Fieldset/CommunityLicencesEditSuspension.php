<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("community-licences-edit-suspension")
 */
class CommunityLicencesEditSuspension
{
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
    public $reasons = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $status = null;

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
}
