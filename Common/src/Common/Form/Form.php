<?php

/**
 * Form
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form;

use Zend\Form as ZendForm;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Form
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Form extends ZendForm\Form
{
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
                if (!array_filter($input) && 3 == count($input)) {
                    $input = null;
                } else {
                    $input = $this->setEmptyDataselectArraysToNull($input);
                }
            }
        }

        return $data;
    }
}
