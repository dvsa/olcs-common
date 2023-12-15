<?php
namespace CommonTest\Common\Form\Model\Form\Licence\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 */
class PersonFormStub
{
    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Licence\Fieldset\Person")
     */
    public $data = null;
}
