<?php

namespace Common\Form;

use ReflectionClass;
use Zend\Form as ZendForm;

/**
 * Form
 */
class Form extends ZendForm\Form
{
    /**
     * Form constructor. Prevents browser HTML5 form validations
     *
     * @param null $name Form Name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('novalidate', 'novalidate');
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * Clone
     *
     * @return void
     */
    public function __clone()
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        foreach ($props as $prop) {
            $name = $prop->getName();

            $value = $this->$name;
            if (is_object($value)) {
                $this->$name = clone $this->$name;
            }
        }
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
