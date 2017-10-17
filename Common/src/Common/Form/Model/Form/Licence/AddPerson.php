<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 17/10/2017
 * Time: 12:12
 */

namespace Common\Form\Model\Form\Licence;

/**
 *
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
     * @Form\ComposedObject("Common\Form\Model\Form\Licence\Fieldset\AddPersonFormActions")
     * @Form\Attributes({"class":"actions-container"})
     */
    public $formActions = null;
}
