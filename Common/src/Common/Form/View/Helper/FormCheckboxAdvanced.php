<?php

namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\View\Renderer\PhpRenderer;

/**
 * Helper to render the advanced checkbox control for continuation
 */
class FormCheckboxAdvanced extends \Common\Form\View\Helper\Extended\FormCollection
{
    /**
     * Render
     *
     * @param ElementInterface $element Element to render
     *
     * @return string HTML
     */
    public function render(ElementInterface $element)
    {
        /** @var PhpRenderer $v */
        $view = $this->view;
        return $view->partial(
            'partials/form/checkbox-advanced',
            ['element' => $element, 'content' => $element->getOption('content'), 'data' => $view->data]
        );
    }
}
