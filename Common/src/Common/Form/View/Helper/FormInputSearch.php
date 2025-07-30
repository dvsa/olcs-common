<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\View\Renderer\PhpRenderer;

class FormInputSearch extends Extended\FormCollection
{
    #[\Override]
    public function render(ElementInterface $element): string
    {
        return $this->view->render(
            'partials/form/input-search',
            array_merge($this->view->vars()->getArrayCopy(), ['fieldsetElement' => $element])
        );
    }
}
