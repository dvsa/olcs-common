<?php

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
     * @param array  $data             Row data
     * @param array  $column           Colunm params
     * @param string $formattedContent Content
     *
     * @return string
     */
    public function render($data, $column, $formattedContent = null)
    {
        $prefix = null;

        /** @var \Laminas\I18n\Translator\Translator $translator */
        $translator = $this->getTable()->getServiceLocator()->get('translator');

        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'A':
                    $prefix = 'common.table.status.new';
                    break;
                case 'U':
                    $prefix = 'common.table.status.updated';
                    break;
                case 'C':
                    $prefix = 'common.table.status.current';
                    $column['action-attributes'][] = 'disabled="disabled"';
                    break;
                case 'D':
                    $prefix = 'common.table.status.removed';
                    $column['action-attributes'][] = 'disabled="disabled"';
                    break;
            }
        }

        $prefix = ($prefix !== null ? '(' . $translator->translate($prefix) . ') ' : '');

        $content = parent::render($data, $column, $formattedContent);

        return $prefix . trim($content);
    }
}
