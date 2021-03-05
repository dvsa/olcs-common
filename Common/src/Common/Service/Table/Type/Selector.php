<?php

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Type;

use Common\Util\Escape;

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Selector extends AbstractType
{
    protected $format = '<input type="radio" name="%s" value="%s" %s />';

    /**
     * Render the selector
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

        if (isset($column['aria-attributes'])) {
            foreach ($column['aria-attributes'] as $attrName => $attrValue) {
                if (is_callable($attrValue)) {
                    $attrValue = $attrValue($data, $this->getTable()->getTranslator());
                }
                $attributes[] = 'aria-' . $attrName . '="' . Escape::html($attrValue) . '"';
            }
        }

        if (isset($column['disableIfRowIsDisabled']) && $this->getTable()->isRowDisabled($data)) {
            $attributes[] = 'disabled="disabled"';
        }

        if (isset($column['disabled-callback'])) {
            $callback = $column['disabled-callback'];
            if ($callback($data)) {
                $attributes[] = 'disabled="disabled"';
            }
        }

        // allow setting the data index name that contains the id value
        $idx = 'id';
        if (isset($column['idIndex'])) {
            $idx = $column['idIndex'];
        }

        $attributes[] = 'id="'. $fieldset . '[id][' . $data[$idx] .']"';

        return sprintf($this->format, $name, $data[$idx], implode(' ', $attributes));
    }
}
