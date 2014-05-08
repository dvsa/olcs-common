<?php

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Type;

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Selector extends AbstractType
{
    private $format = '<input type="radio" name="%s" value="%s" />';

    /**
     * Render the selector
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public function render($data, $column)
    {
        $fieldset = $this->getTable()->getFieldset();

        $name = 'id';

        if (!empty($fieldset)) {
            $name = $fieldset . '[id]';
        }

        return sprintf($this->format, $name, $data['id']);
    }
}
