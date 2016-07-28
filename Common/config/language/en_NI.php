<?php

$gb = include(__DIR__ . '/en_GB.php');

$ni = array_merge(
    $gb,
    [
        'TM1_FORM_LINK' => 'http://www.doeni.gov.uk/gv_ni_79___tm_ni_1___transport_manager_application_v_2.2.pdf',
        'selfserve-app-subSection-previous-history-criminal-conviction-hasConv' => 'Has any person named in this
            application (including partners, directors and Transport Managers); any company of which a person named on
            this application is or has been a director; any parent company if you are a limited company; received any
            penalties or have currently any unspent convictions?',
        'selfserve-app-subSection-previous-history-criminal-conviction-tableEmptyMessage' => 'Please add any relevant
            offences which must be declared to the Dfl.',
        'selfserve-app-subSection-previous-history-criminal-conviction-labelConfirm' => 'Please tick to confirm that you
            are aware that you must tell the Dfl immediately of any relevant convictions that occur between the
            submission of your application and a decision being made on this application.',
        'application_previous-history_licence-history_prevHasLicence' => 'Does anyone you\'ve named already have an operator\'s licence in Great Britain or Northern Ireland?',
        'application_previous-history_licence-history_prevHadLicence' => 'Has anyone you\'ve named ever had or applied for an operator\'s licence in Great Britain or Northern Ireland?',
        'application_previous-history_licence-history_prevBeenDisqualifiedTc' => 'Has anyone you\'ve named ever been disqualified from having an operator\'s licence in Great Britain or Northern Ireland?',
        'application_previous-history_licence-history_prevBeenAtPi' => 'Has anyone you\'ve named ever taken part in a public inquiry held by the Dfl Northern Ireland or a GB Traffic Commissioner?',
        'application_previous-history_licence-history_prevPurchasedAssets' => 'In the past 12 months, has anyone you\'ve named bought assets or shares in a company that holds or has held an operator\'s licence in Great Britain or Northern Ireland?',
    ]
);

return $ni;
