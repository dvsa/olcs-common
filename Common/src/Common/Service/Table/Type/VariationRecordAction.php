<?php

/**
 * Variation Record Action type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Type;

/**
 * Variation Record Action type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationRecordAction extends Action
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

        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'A':
                    $prefix = '(New)';
                    break;
                case 'U':
                    $prefix = '(Updated)';
                    break;
                case 'C':
                    $prefix = '(Current)';
                    $column['action-attributes'][] = 'disabled="disabled"';
                    break;
                case 'D':
                    $prefix = '(Removed)';
                    $column['action-attributes'][] = 'disabled="disabled"';
                    break;
            }
        }

        $content = parent::render($data, $column, $formattedContent);

        return trim($prefix . ' ' . $content);
    }
}
