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
     */
    protected function prepareAttributes(array $attributes): array
    {
        foreach ($attributes as $key => $value) {
            $attribute = strtolower($key);

            if (
                str_starts_with($attribute, 'aria-')
                || str_starts_with($attribute, 'x-')
            ) {
                $this->translatableAttributes += [$attribute => true];
            }

            if (
                !isset($this->validGlobalAttributes[$attribute])
                && !isset($this->validTagAttributes[$attribute])
                && !str_starts_with($attribute, 'data-')
                && !str_starts_with($attribute, 'aria-')
                && !str_starts_with($attribute, 'x-')
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

            $stringValue = (string) $value;

            // Use the string key and value
            $attributes[$key] = $stringValue;
        }

        return $attributes;
    }
}
