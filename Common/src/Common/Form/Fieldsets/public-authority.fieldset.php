<?php
return
[    
    'name' => 'public-authority',
    'elements' => 
    [
        'company_name' => 
        [
            'type' => 'companyName',
        ],
        'trading_names' => 
        [
            'type' => 'tradingNames',              
        ],
        'submit_add_trading_name' => 
        [
            'name' => 'submit_add_trading_name',
            'type' => 'addAnotherButton',
        ],
        [
            'type' => 'businessType'
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