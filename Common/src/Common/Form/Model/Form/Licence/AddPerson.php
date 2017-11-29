<?php

namespace Common\Form\Model\Form\Licence;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lic-people")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class AddPerson
{

    /**
     * @Form\Attributes({"class":"add-multiple"})
     * @Form\ComposedObject({
     *     "target_object":"Common\Form\Model\Form\Licence\Fieldset\Person",
     *     "is_collection":true,
     *     "options":{
     *         "count": 1,
     *         "hint":"markup-add-another-director-hint",
     *         "hint-position": "below",
     *         "should_create_template": true,
     *     }
     * })
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
