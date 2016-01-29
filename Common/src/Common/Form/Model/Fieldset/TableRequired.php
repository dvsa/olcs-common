<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("table")
 */
class TableRequired
{
    /**
     * @Form\Options({"label":"row"})
     * @Form\AllowEmpty(false)
     * @Form\Required(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Type("\Common\Form\Elements\Types\Table")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\TableRequiredValidator",
     *     "options":{"label":"record"}
     * })
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
