<?php

/**
 * Custom Selector type
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\Service\Table\Type;

/**
 * Custom Selector type
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CustomSelector extends AbstractType
{
    protected $format = '<input type="radio" name="%s" value="%s" %s />';

    /**
     * Render custom selector. Allows overiding the name field and the data used to generate the value
     * To override name Use existing $column['name'] default is 'id'
     * To override value element use new $column['data-field'] default is 'id'
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

        if (isset($column['name'])) {
            $name = $column['name'];
        }

        if (!empty($fieldset)) {
            $name = $fieldset . '[' . $name . ']';
        }

        $dataField = 'id';

        if (isset($column['data-field'])) {
            $dataField = $column['data-field'];
        }

        $attributes = [];

        if (isset($column['data-attributes'])) {
            foreach ($column['data-attributes'] as $attrName) {
                if (isset($data[$attrName])) {
                    if (is_array($data[$attrName]) && isset($data[$attrName]['id'])) {
                        $attributes[] = 'data-' . $attrName . '="' . $data[$attrName]['id'] . '"';
                    } else {
                        $attributes[] = 'data-' . $attrName . '="' . $data[$attrName] . '"';
                    }
                }
            }
        }

        if (isset($column['disableIfRowIsDisabled']) && $this->getTable()->isRowDisabled($data)) {
            $attributes[] = 'disabled="disabled"';
        }

        return sprintf($this->format, $name, $data[$dataField], implode(' ', $attributes));
    }
}
