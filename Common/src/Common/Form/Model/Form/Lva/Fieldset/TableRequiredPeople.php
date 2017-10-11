<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;
use Common\Form\Model\Fieldset\TableRequired;

/**
 * @Form\Name("table")
 */
class TableRequiredPeople extends TableRequired
{
    /**
     * @Form\Required(false)
     * @Form\Type("Hidden")
     * @Form\Validator({
     *     "name":"Common\Form\Elements\Validators\TableRequiredValidator",
     *     "options":{"label":"person", "rowsRequired":1}
     * })
     * @Form\Validator({"name":"Zend\Validator\NotEmpty","options":{"array"}})
     */
    public $rows;


    /**
     * @Form\Options({"label":"row"})
     * @Form\Type("\Common\Form\Elements\Types\Table")
     */
    public $table;
}
