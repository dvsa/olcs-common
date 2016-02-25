<?php

namespace Common\Service\Table\Type;

/**
 * DeltaActionLinks
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeltaActionLinks extends Selector
{
    public function render($data, $column, $formattedContent = null)
    {
        // @todo translate Remove, Restore

        if ($this->isRestoreVisible($data, $column)) {
            return sprintf('<input type="submit" class="" name="table[action][restore][%s]" value="Restore">', $data['id']);
        }

        if ($this->isRemoveVisible($data, $column)) {
            return sprintf('<input type="submit" class="" name="table[action][delete][%s]" value="Remove">', $data['id']);
        }
    }

    /**
     * Is the Remove link visible
     *
     * @param array $data
     * @param array $column
     *
     * @return bool
     */
    private function isRemoveVisible($data, $column)
    {
        return isset($data['action']) && !in_array($data['action'], ['C', 'D']);
    }

    /**
     * Is the Restore link visible
     *
     * @param array $data
     * @param array $column
     *
     * @return bool
     */
    private function isRestoreVisible($data, $column)
    {
        // Default to checking "action" being C (current) or D (deleted)
        return isset($data['action']) && in_array($data['action'], ['C', 'D']);
    }
}
