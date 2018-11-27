<?php

namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;

class FormMultiCheckBoxContent extends \Zend\Form\View\Helper\FormMultiCheckbox
{
    public function render(ElementInterface $element)
    {
        var_dump($element);
        exit;
    }
}
