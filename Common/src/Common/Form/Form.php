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

    public function setData($data)
    {
        $data = $this->setEmptyDataselectArraysToNull($data);
        return parent::setData($data);
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
                if (!array_filter($input) && (count($input) === 3 || count($input) === 5)) {
                    $input = null;
                } else {
                    $input = $this->setEmptyDataselectArraysToNull($input);
                }
            }
        }

        return $data;
    }
}
