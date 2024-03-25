<?php

/**
 * Prepare Attributes Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper\Extended;

/**
 * Prepare Attributes Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait PrepareAttributesTrait
{
    /**
     * Prepare attributes for rendering
     *
     * Ensures appropriate attributes are present (e.g., if "name" is present,
     * but no "id", sets the latter to the former).
     *
     * Removes any invalid attributes
     *
     * @param array $attributes Attributes
     *
     * @return array
     */
    protected function prepareAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            $attribute = strtolower($key);

            if (
                0 === strpos($attribute, 'aria-')
                || 0 === strpos($attribute, 'x-')
            ) {
                $this->translatableAttributes += [$attribute => true];
            }

            if (!isset($this->validGlobalAttributes[$attribute])
                && !isset($this->validTagAttributes[$attribute])
                && 'data-' != substr($attribute, 0, 5)
                && 'aria-' != substr($attribute, 0, 5)
                && 'x-' != substr($attribute, 0, 2)
            ) {
                // Invalid attribute for the current tag
                unset($attributes[$key]);
                continue;
            }

            // Normalize attribute key, if needed
            if ($attribute != $key) {
                unset($attributes[$key]);
                $attributes[$attribute] = $value;
            }

            // Normalize boolean attribute values
            if (isset($this->booleanAttributes[$attribute])) {
                $attributes[$attribute] = $this->prepareBooleanAttributeValue($attribute, $value);
            }
        }

        return $attributes;
    }
}
