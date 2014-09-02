<?php

$translationPrefix = 'application_taxi-phv_licence';
$translationPrefixTrafficArea = $translationPrefix . '.trafficArea';

return array(
    $translationPrefix => array(
        'name' => $translationPrefix,
        'attributes' => array(
            'method' => 'post',
        ),
        'fieldsets' => array(
            array(
                'name' => 'table',
                'options' => array(),
                'type' => 'table-required'
            ),
            array(
                'name' => 'dataTrafficArea',
                'elements' => array(
                    'trafficArea' => array(
                        'type' => 'select',
                        'value_options' => array(),
                        'required' => true,
                        'label' => $translationPrefixTrafficArea . '.label.new',
                        'hint' => $translationPrefixTrafficArea . '.hint.new',
                    ),
                    'trafficAreaInfoLabelExists' => array(
                        'type' => 'htmlTranslated',
                        'attributes' => array(
                            'value' => $translationPrefixTrafficArea . '.label.exists'
                        )
                    ),
                    'trafficAreaInfoNameExists' => array(
                        'type' => 'html',
                        'attributes' => array(
                            'value' => '<b>%NAME%</b>'
                        ),
                    ),
                    'trafficAreaInfoHintExists' => array(
                        'type' => 'htmlTranslated',
                        'attributes' => array(
                            'value' => $translationPrefixTrafficArea . '.labelasahint.exists'
                        )
                    ),
                    'hiddenId' => array(
                        'type' => 'hidden'
                    )
                )
            ),
            array(
                'type' => 'journey-buttons'
            )
        )
    )
);
