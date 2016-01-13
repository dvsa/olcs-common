<?php

namespace Common\Service\Table\Type;

/**
 * OperatingCentreAction type
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OperatingCentreAction extends Action
{
    /**
     * Render the selector
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public function render($data, $column, $formattedContent = null)
    {
        $prefix = '';
        if (isset($data['s4']) && $data['s4'] !== null) {
            $prefix = '(Schedule 4/1)';
        }

        $content = parent::render($data, $column, $formattedContent);

        return trim($prefix . ' ' . $content);
    }
}
