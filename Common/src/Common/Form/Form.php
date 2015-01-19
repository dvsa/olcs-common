<?php

namespace Common\Form;

use Zend\Form as ZendForm;

/**
 * Form
 */
class Form extends ZendForm\Form
{
    public function __toString()
    {
        return get_class($this);
    }
}
