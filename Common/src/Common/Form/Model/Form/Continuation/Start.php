<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Start
{
    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"continuations.start-page.review-licence-label"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
