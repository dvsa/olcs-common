<?php

/**
 * Checkbox type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Type;

/**
 * Checkbox type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Checkbox extends Selector
{
    protected $format = '<input type="checkbox" name="%s[]" value="%s" %s />';

    /**
     * Render the checkbox
     *
     * @param array $data
     * @param array $column
     * @param string $formattedContent
     *
     * @return string
     */
    public function render($data, $column, $formattedContent = null)
    {
        $fieldset = $this->getTable()->getFieldset();
        $name = 'id';

        if (!empty($fieldset)) {
            $name = $fieldset . '[id]';
        }

        $attributes = [];

        if (isset($column['disableIfRowIsDisabled']) && $this->getTable()->isRowDisabled($data)) {
            $attributes[] = 'disabled="disabled"';
        }

        return sprintf($this->format, $name, $data['id'], implode(' ', $attributes));
    }
}
