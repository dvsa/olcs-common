<?php
/**
 * Base element. Should not have a custom type added to it. This can be set in
 * in the form config for the element by using the filter attribute or create new element
 */
return ['checkbox-boolean' =>
        [
            'type' => '\Common\Form\Elements\InputFilters\Checkbox',
            'options' => [
                'checked_value' => 1,
                'unchecked_value' => 0,
                'must_be_checked' => false
            ],
        ]
    ];
