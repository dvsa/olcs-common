<?php

namespace Common\Service\Table\Type;

/**
 * OperatingCentreVariationRecordAction type
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OperatingCentreVariationRecordAction extends VariationRecordAction
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
        $content = parent::render($data, $column, $formattedContent);

        if (isset($data['s4']) && $data['s4'] !== null) {
            $content = str_replace('<button', ' (Schedule 4/1) <button', $content);
        }

        return trim($content);
    }
}
