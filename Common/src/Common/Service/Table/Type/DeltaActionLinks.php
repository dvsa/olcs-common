<?php

namespace Common\Service\Table\Type;

/**
 * DeltaActionLinks
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DeltaActionLinks extends Selector
{
    /**
     * Render
     *
     * @param array $data
     * @param array $column
     * @param string $formattedContent
     *
     * @return string
     */
    public function render($data, $column, $formattedContent = null)
    {
        $translator = $this->getTable()->getServiceLocator()->get('translator');
        $remove = $translator->translate('delta_action_links.remove');
        $restore = $translator->translate('delta_action_links.restore');

        if ($this->isRestoreVisible($data, $column)) {
            return sprintf(
                '<input type="submit" class="right-aligned action--secondary" '.
                    'name="table[action][restore][%s]" value="' . $restore . '">',
                $data['id']
            );
        }

        if ($this->isRemoveVisible($data, $column)) {
            return sprintf(
                '<input type="submit" class="right-aligned action--secondary trigger-modal" '.
                    'name="table[action][delete][%s]" value="' . $remove . '">',
                $data['id']
            );
        }
    }

    /**
     * Is the Remove link visible
     *
     * @param array $data
     *
     * @return bool
     */
    private function isRemoveVisible($data)
    {
        return isset($data['action']) && !in_array($data['action'], ['C', 'D']);
    }

    /**
     * Is the Restore link visible
     *
     * @param array $data
     *
     * @return bool
     */
    private function isRestoreVisible($data)
    {
        // Default to checking "action" being C (current) or D (deleted)
        return isset($data['action']) && in_array($data['action'], ['C', 'D']);
    }
}
