<?php

declare(strict_types=1);

namespace Common\Form\Element;

/**
 * A form submit button element.
 *
 * @see \CommonTest\Form\Element\SubmitButtonTest
 */
class SubmitButton extends Button
{
    /**
     * @param string $name
     * @param string $label
     */
    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->setAttribute('type', 'submit');
    }
}
