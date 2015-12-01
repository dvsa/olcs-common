<?php

$gb = include(__DIR__ . '/en_GB.php');

$ni = array_merge(
    $gb,
    [
        'TM1_FORM_LINK' => 'http://www.doeni.gov.uk/gv_ni_79___tm_ni_1___transport_manager_application_v_2.2.pdf',
        'application_previous-history_licence-history_prevHasLicence' => 'Does any person named in this application
            (including partners, directors and Transport Managers) currently hold a goods or public service vehicle
            operator\'s licence in GB or Northern Ireland?',
        'application_previous-history_licence-history_prevHadLicence' => 'previously held or applied for a goods or
            public service vehicle operator’s licence in Northern Ireland or GB?',
        'application_previous-history_licence-history_prevBeenAtPi' => 'Ever attended a Public Inquiry before the DOE
            or a GB traffic commissioner?',
        'application_previous-history_licence-history_prevBeenDisqualifiedTc' => 'Been disqualified from holding or
            obtaining an operator\'s licence by DOE, or a GB traffic commissioner?',
        'application_previous-history_licence-history_prevPurchasedAssets' => 'Within the last twelve months, have
            you, your company or organisation or your partners or directors purchased the assets or shareholding of any
            company that, to your knowledge, currently holds or has previously held an operator\'s licence in Northern
            Ireland or GB?',
        'selfserve-app-subSection-previous-history-criminal-conviction-hasConv' => 'Has any person named in this
            application (including partners, directors and Transport Managers); any company of which a person named on
            this application is or has been a director; any parent company if you are a limited company; received any
            penalties or have currently any unspent convictions?',
        'selfserve-app-subSection-previous-history-criminal-conviction-tableEmptyMessage' => 'Please add any relevant
            offences which must be declared to the DOE.',
        'selfserve-app-subSection-previous-history-criminal-conviction-labelConfirm' => 'Please tick to confirm that you
            are aware that you must tell the DOE immediately of any relevant convictions that occur between the
            submission of your application and a decision being made on this application.',
    ]
);

return $ni;
