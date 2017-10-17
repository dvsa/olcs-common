<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 17/10/2017
 * Time: 12:12
 */

namespace Common\Form\Model\Form\Licence;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("lic-people")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class AddPerson
{

    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Licence\Fieldset\Person")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\FormCrudActionsPerson")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;

}