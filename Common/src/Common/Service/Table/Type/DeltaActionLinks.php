<?php

namespace Common\Service\Table\Type;

use Common\Util\Escape;

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
        $ariaDescription = $this->getAriaDescription($data, $column, $translator);

        if ($this->isRestoreVisible($data, $column)) {
            $restore = $translator->translate('action_links.restore');
            $restoreAria = $translator->translate('action_links.restore.aria');
            $ariaLabel = sprintf(self::ARIA_LABEL_FORMAT, $restoreAria, $ariaDescription);

            return sprintf(
                '<input type="submit" class="right-aligned action--secondary" '.
                    'name="table[action][restore][%s]" aria-label="%s" value="%s">',
                $data['id'],
                Escape::htmlAttr($ariaLabel),
                Escape::htmlAttr($restore)
            );
        }

        if ($this->isRemoveVisible($data, $column)) {
            $remove = $translator->translate('action_links.remove');
            $removeAria = $translator->translate('action_links.remove.aria');
            $ariaLabel = sprintf(self::ARIA_LABEL_FORMAT, $removeAria, $ariaDescription);

            return sprintf(
                '<input type="submit" class="right-aligned action--secondary trigger-modal" '.
                    'name="table[action][delete][%s]" aria-label="%s" value="%s">',
                $data['id'],
                Escape::htmlAttr($ariaLabel),
                Escape::htmlAttr($remove)
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
