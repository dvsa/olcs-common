<?php
return array(
    'view_helpers' => array(
        'invokables' => array(
            'form' => 'Common\Form\View\Helper\Form',
            'formRow' => 'Common\Form\View\Helper\FormRow',
            'formElement' => 'Common\Form\View\Helper\FormElement',
            'formElementErrors' => 'Common\Form\View\Helper\FormElementErrors',
            'formErrors' => 'Common\Form\View\Helper\FormErrors',
            'htmlTable'     => 'Common\View\Helper\HtmlTable',
            'crudHtmlTable' => 'Common\View\Helper\CrudHtmlTable'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
           'partials/view' => __DIR__ . '/../view'
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo'
            )
        )
            ),
    'forms_path' => __DIR__ .'/../../Common/src/Common/Form/Forms/',
    'tables' => array(
        'config' => array(
            __DIR__ . '/../src/Common/Table/Tables/'
        ),
        'partials' => __DIR__ . '/../view/table/'
    ),
    'sic_codes_path' => __DIR__ .'/../../Common/config/sic-codes',
    'fieldsets_path' => __DIR__ .'/../../Common/src/Common/Form/Fieldsets/',
    'static-list-data' => include __DIR__ . '/list-data/static-list-data.php',
    'form' => array(
        'elements' =>  include __DIR__ . '/../src/Common/Form/Elements/getElements.php'
    ),
    //-------- Start navigation -----------------
    'navigation' => array(
        'default' => array(
            include __DIR__ . '/navigation.config.php'
        )
    ),
    //-------- End navigation -----------------

    //-------- Start service API mappings -----------------
    'service_api_mapping' => array(
        'apis' => array(
            'payments' => array(
                'Vosa\Payment\Token' => 'token',
                'Vosa\Payment\Db' => 'paymentdb',
                'Vosa\Payment\Card' => 'cardpayment'
            ),
            'backend' => array(
                'User' => 'user',
                'Person' => 'person',
                'PersonSearch' => 'person-search',
                'OperatorSearch' => 'operator-search',
                'VosaCase' => 'vosa-case',
                'Licence' => 'licence',
                'LicenceOrganisation' => 'licence-organisation',
                'TrafficArea' => 'traffic-area',
                'Organisation' => 'organisation',
                'Application' => 'application',
                'ApplicationOperatingCentre' => 'application-operating-centre',
                'LicenceVehicle' => 'licence-vehicle',
                'Submission' => 'submission',
                'SubmissionAction' => 'submission-action',
                'SubmissionActionStatus' => 'submission-action-status',
                'SubmissionActionStatusType' => 'submission-action-status-type',
                'Statement' => 'statement',
                'Appeal' => 'appeal',
                'Conviction' => 'conviction',
                'ApplicationOperatingCentre' => 'application-operating-centre',
                'Application' => 'application',
                'Vehicle' => 'vehicle',
                'Stay' => 'stay',
                'Address' => 'address'
            )
        )
    )
     //-------- End service API mappings -----------------
);
