<?php

namespace Common\Service\Table\Formatter\TaskAllocationRule;

/**
 * User value for a task allocation rule
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class User extends \Common\Service\Table\Formatter\Name
{
    /**
     * User value for a task allocation rule
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $userName = parent::format($data, $column, $sm);
        if (!empty(trim($userName))) {
            return $userName;
        }

        if (is_array($data['taskAlphaSplits']) && count($data['taskAlphaSplits']) > 0) {
            return '[Alpha split]';
        }

        return 'Unassigned';
    }
}
