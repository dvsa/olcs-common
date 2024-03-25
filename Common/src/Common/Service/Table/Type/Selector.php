<?php

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Type;

use Common\Util\Escape;
use Laminas\Mvc\I18n\Translator;

/**
 * Selector type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Selector extends AbstractType
{
    /**
     * translation keys
     * used by the extending ActionLinks and DeltaActionLinks classes
     */
    public const ARIA_LABEL_FORMAT = '%s (%s)';

    public const KEY_ACTION_LINKS_REMOVE = 'action_links.remove';

    public const KEY_ACTION_LINKS_REMOVE_ARIA = 'action_links.remove.aria';

    public const KEY_ACTION_LINKS_REPLACE = 'action_links.replace';

    public const KEY_ACTION_LINKS_REPLACE_ARIA = 'action_links.replace.aria';

    public const KEY_ACTION_LINKS_RESTORE = 'action_links.restore';

    public const KEY_ACTION_LINKS_RESTORE_ARIA = 'action_links.restore.aria';

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

    /**
     * Work out the description of a table row or field for accessibility reasons
     * Used by the extending ActionLinks and DeltaActionLinks classes
     * These build up their button in a different way and don't use the render method from this class
     * At some point it'd be good if they did
     */
    protected function getAriaDescription(array $data, array $column, Translator $translator): string
    {
        //if we've no config default to using the id of the record
        if (!isset($column['ariaDescription'])) {
            return 'id ' . $data['id'];
        }

        //if we've got a function to generate the description
        if (is_callable($column['ariaDescription'])) {
            return $column['ariaDescription']($data, $column);
        }

        //if the value is the name of a column in the data
        if (isset($data[$column['ariaDescription']])) {
            return $translator->translate($data[$column['ariaDescription']]);
        }

        //if none of the other conditions match, take the value of ariaDescription and attempt to translate it
        return $translator->translate($column['ariaDescription']);
    }
}
