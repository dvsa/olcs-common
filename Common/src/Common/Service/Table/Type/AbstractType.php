<?php

namespace Common\Service\Table\Type;

use Common\Service\Table\TableBuilder;

/**
 * Abstract type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractType
{
    /**
     * Holds the table
     *
     * @var TableBuilder
     */
    protected $table;

    /**
     * DI the table
     *
     * @param TableBuilder $table Table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Getter for table
     *
     * @return TableBuilder
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Render the selector
     *
     * @param array  $data             Data
     * @param array  $column           Column params
     * @param string $formattedContent Content
     *
     * @return string
     */
    abstract public function render($data, $column, $formattedContent = null);
}
