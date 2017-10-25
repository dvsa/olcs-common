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
     * @Form\ComposedObject("Common\Form\Model\Form\Licence\Fieldset\Person")
     * @Form\Attributes({"class":""})
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Licence\Fieldset\AddPersonFormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
