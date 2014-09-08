<?php

$groups = array(
    array(
        'title' => 'Type of licence',
        'forms' => array(
            'application_type-of-licence_operator-location',
            'application_type-of-licence_operator-type',
            'application_type-of-licence_licence-type'
        )
    ),
    array(
        'title' => 'Operating Centres',
        'forms' => array(
            'application_operating-centres_authorisation'
        )
    ),
    array(
        'title' => 'Previous history',
        'forms' => array(
            'application_previous-history_financial-history',
            // this should be implemented in another story
            //'application_previous-history_licence-history',
            'application_previous-history_convictions-penalties'
        )
    ),
    array(
        'title' => 'Vehicles & Safety',
        'forms' => array(
            'application_vehicle-safety_safety',
            'application_vehicle-safety_undertakings'
        )
    )

);

$formFieldsets = [];

$ignoreFieldsetTypes = array(
    'journey-buttons'
);

foreach ($groups as $key => $group) {

    $idx = $key + 1;

    $formFieldsets[] = array(
        'name' => 'title-' . $idx,
        'elements' => array(
            'title' => array(
                'type' => 'html',
                'attributes' => array(
                    'value' => '<h2>' . $idx . '. ' . $group['title'] . '</h2>'
                )
            )
        )
    );

    foreach ($group['forms'] as $form) {

        $config = include(__DIR__ . '/' . $form . '.form.php');
        $fieldsets = $config[$form]['fieldsets'];

        foreach ($fieldsets as $key => $fieldset) {

            // Some places need the unmapped fieldset name, so we set it as an
            // attribute here.
            if ( isset($fieldset['name']) ) {
                $fieldset['attributes']['unmappedName']=$fieldset['name'];
            }

            $i = $key + 1;

            if (isset($fieldset['type']) && in_array($fieldset['type'], $ignoreFieldsetTypes)) {
                continue;
            }

            $fieldset['name'] = $form . '-' . $i;

            $formFieldsets[] = $fieldset;
        }
    }
}

return array(
    'application_review-declarations_summary' => array(
        'name' => 'application_review-declarations_summary',
        'disabled' => true,
        'fieldsets' => array_merge(
            $formFieldsets,
            array(
                array(
                    'type' => 'journey-buttons'
                )
            )
        )
    )
);
