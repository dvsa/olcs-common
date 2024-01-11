<?php

namespace Common\Form\Elements\Types;

use Common\Module;
use Common\View\Helper\DateTime as DateTimeViewHelper;

/**
 * Html DateTime Element
 */
class HtmlDateTime extends Html
{
    /**
     * Set the element value
     *
     * @param mixed $value Value
     *
     * @return HtmlDateTime
     */
    public function setValue($value)
    {
        $this->value = !empty($value)
            ? (new DateTimeViewHelper())->__invoke(new \DateTime($value), Module::$dateTimeSecFormat)
            : null;

        return $this;
    }
}
