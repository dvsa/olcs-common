<?php

namespace Common\InputFilter;

/**
 * @see \CommonTest\InputFilter\InputTest
 * @deprecated Use \Common\InputFilter\ChainValidatedInput
 */
class Input extends \Laminas\InputFilter\Input
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
