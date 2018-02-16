<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("ConvictionsPenaltiesReadMoreLink")
 */
class ConvictionsPenaltiesReadMoreLink
{
    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Attributes({"value":"<a href=""%s"" target=""_blank"">%s</h3>"})
     * @Form\Options({"tokens": {"convictions-read-more-link","Read more about convictions"}})
     */
    public $readMoreLink = null;
}
