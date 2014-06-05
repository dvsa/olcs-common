<?php

/**
 * Form
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form;

use Zend\Form as ZendForm;
use Common\Form\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Form
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Form extends ZendForm\Form
{
    /**
     * Retrieve input filter used by this form
     *
     * @return null|InputFilterInterface
     */
    public function getInputFilter()
    {
        if ($this->object instanceof InputFilterAwareInterface) {
            if (null == $this->baseFieldset) {
                $this->filter = $this->object->getInputFilter();
            } else {
                $name = $this->baseFieldset->getName();
                if (!$this->filter instanceof InputFilterInterface || !$this->filter->has($name)) {
                    $filter = new InputFilter();
                    $filter->add($this->object->getInputFilter(), $name);
                    $this->filter = $filter;
                }
            }
        }

        if (!isset($this->filter)) {
            $this->filter = new InputFilter();
        }

        if (!$this->hasAddedInputFilterDefaults
            && $this->filter instanceof InputFilterInterface
            && $this->useInputFilterDefaults()
        ) {
            $this->attachInputFilterDefaults($this->filter, $this);
            $this->hasAddedInputFilterDefaults = true;
        }

        return $this->filter;
    }
}
