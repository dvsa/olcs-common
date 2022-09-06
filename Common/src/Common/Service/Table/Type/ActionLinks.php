<?php

namespace Common\Service\Table\Type;

use Common\Util\Escape;

/**
 * ActionLinks type
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ActionLinks extends Selector
{
    const DEFAULT_INPUT_NAME = 'table[action][delete][%d]';

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
        $remove = $translator->translate('action_links.remove');
        $removeAria = $translator->translate('action_links.remove.aria');
        $replace = $translator->translate('action_links.replace');
        $replaceAria = $translator->translate('action_links.replace.aria');
        $ariaDescription = $this->getAriaDescription($data, $column, $translator);

        $content = '';

        $content .= $this->renderRemoveLink($data, $column, $remove, $removeAria, $ariaDescription);
        $content .= $this->renderReplaceLink($data, $column, $replace, $replaceAria, $ariaDescription);

        return $content;
    }

    /**
     * Get input name
     *
     * @param array $column
     * @param string $setting
     *
     * @return string
     */
    private function getInputName($column, $setting)
    {
        if (isset($column[$setting])) {
            return $column[$setting];
        }

        return self::DEFAULT_INPUT_NAME;
    }

    /**
     * Is the link visible
     *
     * @param array $data
     * @param array $column
     * @param string $link
     *
     * @return bool
     */
    private function isLinkVisible($data, $column, $link, $default = true)
    {
        $setting = 'is' . $link . 'Visible';
        if (isset($column[$setting]) && is_callable($column[$setting])) {
            return $column[$setting]($data);
        }

        return $default;
    }

    /**
     * Render remove links
     *
     * @param array $data
     * @param array $column
     * @param string $remove
     * @param string $removeAria
     *
     * @return string
     */
    private function renderRemoveLink($data, $column, $remove, $removeAria, $ariaDescription)
    {
        $content = '';
        if ($this->isLinkVisible($data, $column, 'Remove')) {
            $inputName = sprintf($this->getInputName($column, 'deleteInputName'), $data['id']);
            $ariaLabel = sprintf(self::ARIA_LABEL_FORMAT, $removeAria, $ariaDescription);

            $classes = $this->getClasses($column);
            $content .= sprintf(
                '<input type="submit" class="%s" name="%s" aria-label="%s" value="%s">',
                Escape::htmlAttr($classes),
                Escape::htmlAttr($inputName),
                Escape::htmlAttr($ariaLabel),
                Escape::htmlAttr($remove)
            );
        }
        return $content;
    }

    private function getClasses($column): string
    {
        if (isset($column['actionClasses'])) {
            return $column['actionClasses'];
        }

        $modalClass = ($this->useModal($column)) ? ' trigger-modal' :'';
        return 'right-aligned action--secondary' . $modalClass;
    }

    /**
     * Render replace links
     *
     * @param array $data
     * @param array $column
     * @param string $replace
     * @param string $replaceAria
     *
     * @return string
     */
    private function renderReplaceLink($data, $column, $replace, $replaceAria, $ariaDescription)
    {
        $content = '';
        if ($this->isLinkVisible($data, $column, 'Replace', false)) {
            $inputName = sprintf($this->getInputName($column, 'replaceInputName'), $data['id']);
            $ariaLabel = sprintf(self::ARIA_LABEL_FORMAT, $replaceAria, $ariaDescription);

            $content .= sprintf(
                ' <input type="submit" class="action--secondary right-aligned trigger-modal" name="%s" aria-label="%s" value="%s">',
                Escape::htmlAttr($inputName),
                Escape::htmlAttr($ariaLabel),
                Escape::htmlAttr($replace)
            );
        }
        return $content;
    }

    /**
     * Should a modal be used?
     *
     * @param array $column Column data
     *
     * @return bool
     */
    private function useModal($column)
    {
        if (!isset($column['dontUseModal'])) {
            return true;
        }

        return $column['dontUseModal'] !== true;
    }
}
