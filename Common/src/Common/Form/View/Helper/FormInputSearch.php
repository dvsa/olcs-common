<?php
declare(strict_types=1);

namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\View\Renderer\PhpRenderer;

class FormInputSearch extends Extended\FormCollection
{
    public function render(ElementInterface $element)
    {
        return $this->view->render(
            'partials/form/input-search',
            array_merge($this->view->vars()->getArrayCopy(), ['fieldsetElement' => $element])
        );
    }
}
