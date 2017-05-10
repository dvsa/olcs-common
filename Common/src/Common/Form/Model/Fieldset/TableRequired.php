<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("table")
 */
class TableRequired
{
    /**
     * @Form\Required(true)
     * @Form\Type("Hidden")
     * @Form\Attributes({"value":""})
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\TableRequiredValidator",
     *     "options":{"label":"record"}
     * })
     */
    public $rows = null;

    /**
     * @Form\Type("\Common\Form\Elements\Types\Table")
     * @Form\Options({"label":"row"})
     */
    public $table = null;

    /**
     * @Form\Type("\Common\Form\Elements\InputFilters\NoRender")
     * @Form\Attributes({"value":""})
     */
    public $action = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("\Common\Form\Elements\InputFilters\NoRender")
     */
    public $id = null;
}
