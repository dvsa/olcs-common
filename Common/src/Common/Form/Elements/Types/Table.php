<?php

/**
 * Table Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Element;

/**
 * Table Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Table extends Element
{
    /**
     * Hold the table
     *
     * @var object
     */
    private $table;

    /**
     * Setter for table
     *
     * @param object $table
     */
    public function setTable($table)
    {
        $this->table = $table;

        $table->setActionFieldName($this->getName() . '[action]');
    }

    /**
     * Render the table
     *
     * @return string
     */
    public function render()
    {
        return $this->table->render();
    }
}
