<?php
/**
 * Base element. Should not have a custom type added to it. This can be set in
 * in the form config for the element by using the filter attribute or create new element
 */
return ['checkbox-yn' =>
        [
            'type' => 'checkbox',
            'options' => [
                'checked_value' => 'Y',
                'unchecked_value' => 'N'
            ],
        ]
    ];
