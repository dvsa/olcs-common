<?php

namespace Common\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\View\Renderer\PhpRenderer;

/**
 * Helper to render the GDS horizontal radio button pattern
 */
class FormRadioHorizontal extends \Common\Form\View\Helper\Extended\FormCollection
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
        return $view->render(
            'partials/form/radio-horizontal',
            array_merge($view->vars()->getArrayCopy(), ['element' => $element])
        );
    }
}
