<?php

namespace CommonTest\Form\View\Helper\Extended\Stub;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("child-content")
 */
class FormRadioChildContentStub
{
    /**
     * @Form\Attributes({
     *     "value":"a_value",
     *     "id" : "any",
     * })
     * @Form\Type("\Common\Form\Elements\Types\PlainText")
     */
    public $text;
}
