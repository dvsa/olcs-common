<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("search")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Search
{

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({
     *     "label": "Search",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-10"
     * })
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;

    /**
     * @Form\Name("search")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Search")
     */
    public $search = null;

    /**
     * @Form\Name("advanced")
     * @Form\Options({"label":"Advanced search"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\Advanced")
     */
    public $advanced = null;


}

