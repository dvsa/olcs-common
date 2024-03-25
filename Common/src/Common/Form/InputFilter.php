<?php

/**
 * InputFilter
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form;

use Traversable;
use Laminas\Stdlib\ArrayUtils;
use Laminas\InputFilter\InputFilter as LaminasInputFilter;

/**
 * InputFilter
 *
 * @author Someone <someone@valtech.co.uk>
 */
class InputFilter extends LaminasInputFilter
{
    /**
     * Set data to use when validating and filtering
     *
     * @param  array|Traversable $data
     * @throws Exception\InvalidArgumentException
     * @return InputFilterInterface
     */
    public function setData($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s expects an array or Traversable argument; received %s',
                    __METHOD__,
                    (is_object($data) ? get_class($data) : gettype($data))
                )
            );
        }

        if (is_object($data) && !$data instanceof \ArrayAccess) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        $this->data = $this->setEmptyDataselectArraysToNull($data);

        $this->populate();

        return $this;
    }

    /**
     * Sets empty date select arrays to null.
     *
     * @param array $data
     * @return array
     */
    private function setEmptyDataselectArraysToNull($data)
    {
        foreach ($data as &$input) {
            if (is_array($input)) {
                $input = array_filter($input) === [] && 3 == count($input) ? null : $this->setEmptyDataselectArraysToNull($input);
            }
        }

        return $data;
    }
}
