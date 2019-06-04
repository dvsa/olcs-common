<?php

namespace Common\Service\Qa;

use Zend\Form\Element\Text;

class TextFactory
{
    /**
     * Create a textbox element instance with the supplied name
     *
     * @param string $name
     *
     * @return Text
     */
    public function create($name)
    {
        return new Text($name);
    }
}
