<?php

/**
 * Action type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Type;

/**
 * Action type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Action extends AbstractType
{
    /**
     * Holds the format
     *
     * @var string
     */
    private $format = '<input type="submit" class="%s" name="%s" value="%s" />';

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

        $class = isset($column['class']) ? $column['class'] : '';

        $value = (isset($column['name']) && isset($data[$column['name']]) ? $data[$column['name']] : '');

        $name = 'action';

        if (!empty($fieldset)) {
            $name = $fieldset . '[action]';
        }

        $name .= '[' . $column['action'] . '][' . $data['id'] . ']';

        return sprintf($this->format, $class, $name, $value);
    }
}
