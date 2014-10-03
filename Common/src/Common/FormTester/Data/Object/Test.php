<?php

namespace Common\FormTester\Data\Object;

/**
 * Class Test
 * @package Common\FormTester\Data\Object
 */
class Test
{
    /**
     * @var Stack
     */
    protected $stack;
    /**
     * @var array
     */
    protected $values;

    /**
     * @internal \Common\FormTester\Data\Object\Stack $stack
     * @internal \Common\FormTester\Data\Object\Value $values...
     */
    public function __construct()
    {
        $args = func_get_args();
        $this->stack = array_shift($args);
        $this->values = $args;
    }

    /**
     * @return mixed
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        $result  = null;
        foreach ($this->getValues() as $value) {
            if (is_null($result)) {
                $result = $value->isValid();
            } elseif ($value->isValid() xor $result) {
                return true;
            }
        }

        return false;
    }
}