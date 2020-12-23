<?php

namespace Common\InputFilter;

use Laminas\InputFilter\Input as LaminasInput;

/**
 * Class Input
 * @package Common\InputFilter
 */
class Input extends LaminasInput
{
    /**
     * @var mixed
     */
    protected $filteredValue;
    /**
     * @var bool
     */
    protected $hasFiltered = false;

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (!$this->hasFiltered) {
            $this->filteredValue = $this->getFilterChain()->filter($this->value);
            $this->hasFiltered = true;
        }
        return $this->filteredValue;
    }

    /**
     * @param  mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->hasFiltered = false;
        $this->value = $value;
    }
}
