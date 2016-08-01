<?php

$gb = include(__DIR__ . '/cy_GB.php');

$ni = array_merge(
    $gb,
    [
        'application_previous-history_licence-history_prevHasLicence' => '{WELSH} Does any person named in this
            application (including partners, directors and Transport Managers) currently hold a goods or public service
            vehicle operator\'s licence in GB or Northern Ireland?',
        'application_previous-history_licence-history_prevHadLicence' => '{WELSH} previously held or applied for a
            goods or public service vehicle operator’s licence in Northern Ireland or GB?',
        'application_previous-history_licence-history_prevBeenAtPi' => '{WELSH} ever attended a Public Inquiry before
            the DfI or a GB traffic commissioner?',
        'application_previous-history_licence-history_prevBeenDisqualifiedTc' => '{WELSH} Been disqualified from
            holding or obtaining an operator\'s licence by DfI, or a GB traffic commissioner?',
        'application_previous-history_licence-history_prevPurchasedAssets' => '{WELSH} Within the last twelve
            months, have you, your company or organisation or your partners or directors purchased the assets or
            shareholding of any company that, to your knowledge, currently holds or has previously held an operator\'s
            licence in Northern Ireland or GB?',
        'selfserve-app-subSection-previous-history-criminal-conviction-hasConv' => '{WELSH} Has any person named in this
            application (including partners, directors and Transport Managers); any company of which a person named on
            this application is or has been a director; any parent company if you are a limited company; received any
            penalties or have currently any unspent convictions?',
        'selfserve-app-subSection-previous-history-criminal-conviction-tableEmptyMessage' => '{WELSH} Please add any
            relevant offences which must be declared to the DfI.',
        'selfserve-app-subSection-previous-history-criminal-conviction-labelConfirm' => '{WELSH} Please tick to confirm
            that you are aware that you must tell the DfI immediately of any relevant convictions that occur between the
            submission of your application and a decision being made on this application.',
    ]
);

return $ni;
