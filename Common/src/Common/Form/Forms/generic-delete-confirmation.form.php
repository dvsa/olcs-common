<?php

return array(
    'generic-delete-confirmation' => array(
        'name' => 'generic-delete-confirmation',
        'attributes' => array(
            'method' => 'post',
        ),
        'fieldsets' => array(
            array(
                'name' => 'data',
                'options' => array(),
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden',
                        'label' => 'generic-delete-confirmation-label'
                    )
                )
            ),
            array(
                'type' => 'journey-delete-confirm-buttons'
            )
        )
    )
);
