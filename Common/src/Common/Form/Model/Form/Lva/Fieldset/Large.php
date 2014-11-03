<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("large")
 */
class Large
{
    /**
     * @Form\Options({"label":"row"})
     * @Form\Type("\Common\Form\Elements\Types\Table")
     */
    public $table = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("\Common\Form\Elements\InputFilters\NoRender")
     */
    public $action = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $rows = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("\Common\Form\Elements\InputFilters\NoRender")
     */
    public $id = null;
}
