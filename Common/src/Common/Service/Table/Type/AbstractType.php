<?php

/**
 * Abstract type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Type;

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
     * @var object
     */
    protected $table;

    /**
     * DI the table
     *
     * @param object $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Getter for table
     *
     * @return object
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Render the selector
     *
     * @param array $data
     * @param array $column
     * @param string $formattedContent
     *
     * @return string
     */
    abstract public function render($data, $column, $formattedContent = null);
}
