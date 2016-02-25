<?php

namespace Common\Service\Table\Type;

/**
 * Checkbox type
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ActionLinks extends Selector
{
    public function render($data, $column, $formattedContent = null)
    {
        // @todo translate Remove, Restore

        if ($this->isRemoveVisible($data, $column)) {
            $inputName = sprintf($this->getInputName($column), $data['id']);
            return sprintf('<input type="submit" class="" name="%s" value="Remove">', $inputName);
        }
    }

    private function getInputName($column)
    {
        if (isset($column['deleteInputName'])) {
            return $column['deleteInputName'];
        }

        return 'table[action][delete][%d]';
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
        if (isset($column['isRemoveVisible']) && is_callable($column['isRemoveVisible'])) {
            return $column['isRemoveVisible']($data);
        }

        // Default to show it
        return true;
    }
}
