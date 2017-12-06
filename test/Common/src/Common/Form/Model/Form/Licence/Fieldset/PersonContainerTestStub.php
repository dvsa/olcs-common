<?php

namespace CommonTest\Form\Model\Form\Licence\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * This appears to be the only way to test fieldsets in this repo without modifying them. Annotations seem to allow
 * non-fieldset classes to be added as if they were fieldsets.
 *
 * The SUT fieldset is always required to heave prefer_form_input_filter
 * @Form\Options({"prefer_form_input_filter":true})
 */
class PersonContainerTestStub
{
    /**
     * @Form\ComposedObject({
     *     "target_object":"Common\Form\Model\Form\Licence\Fieldset\Person",
     * })
     */
    public $data;
}
