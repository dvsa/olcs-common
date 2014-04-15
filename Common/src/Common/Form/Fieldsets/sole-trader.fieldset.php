<?php
return
[    
    'name' => 'sole-trader',
    'elements' => 
    [
        'trading_names' => 
        [
            'type' => 'tradingNames',              
        ],
        'submit_add_trading_name' => 
        [
            'name' => 'submit_add_trading_name',
            'value' => 'add_trading_name',
            'type' => 'submit',
            'label' => 'Add another',
        ],
        'type_of_business' => 
        [
            'type' => 'businessType',
            'value_options' => 'sic_codes',             
        ],

    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'complete',
        ]
    ],
];
