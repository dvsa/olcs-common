<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("table")
 */
class TableRequiredTransportManager extends TableRequired
{
    /**
     * @Form\Options({"label":"row"})
     * @Form\AllowEmpty(false)
     * @Form\Required(true)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Type("\Common\Form\Elements\Types\Table")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\TableRequiredValidator",
     *     "options":{"label":"Transport Manager"}
     * })
     */
    public $table = null;
}
