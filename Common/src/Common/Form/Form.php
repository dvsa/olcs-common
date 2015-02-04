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

    /**
     * Prevent a form from being validated (and thus saved) if it is set read only
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->getOption('readonly')) {
            return false;
        }

        return parent::isValid();
    }
}
