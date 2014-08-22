<?php

list($allRoutes, $controllers, $journeys) = include(__DIR__ . '/journeys.config.php');

$release = json_decode(file_get_contents(__DIR__ . '/release.json'), true);

$invokeables = array_merge(
    $controllers, array(
        'Common\Controller\File' => 'Common\Controller\FileController',
        'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
    )
);

// @TODO For now we just set the journey routes at top level, we may need to tweak this for each application
$routes = array_merge(
    $allRoutes,
    array(
        // @TODO this route needs overriding in each application
        'application_start' => array(
            'type' => 'segment',
            'options' => array(
                'route' => '/'
            )
        ),
        'getfile' => array(
            'type' => 'segment',
            'options' => array(
                'route' => '/file/:file/:name',
                'defaults' => array(
                    'controller' => 'Common\Controller\File',
                    'action' => 'download'
                )
            )
        )
    )
);

return array(
    'journeys' => $journeys,
    'router' => array(
        'routes' => $routes
    ),
    'controllers' => array(
        'invokables' => $invokeables
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'route101' => array(
                    'options' => array(
                        'route' => 'formrewrite [olcs|common|selfserve]:formnamespace',
                        'defaults' => array(
                            'controller' => 'Common\Controller\FormRewrite',
                            'action' => 'index'
                        )
                    )
                ),
                'route102' => array(
                    'options' => array(
                        'route' => 'formcleanup [olcs|common|selfserve]:formnamespace',
                        'defaults' => array(
                            'controller' => 'Common\Controller\FormRewrite',
                            'action' => 'cleanup'
                        )
                    )
                )
            )
        )
    ),
    'version' => (isset($release['version']) ? $release['version'] : ''),
    'service_manager' => array(
        'services' => array(
            'address' => new \Common\Service\Address\Address()
        ),
        'factories' => array(
            'postcode' => function ($serviceManager) {
                $postcode = new \Common\Service\Postcode\Postcode();
                $postcode->setServiceLocator($serviceManager);
                return $postcode;
            },
            'postcodeTrafficAreaValidator' => function ($serviceManager) {
                $validator = new \Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator();
                $validator->setServiceLocator($serviceManager);
                return $validator;
            },
        )
    ),
    'file_uploader' => array(
        'default' => 'DiskStore',
        'config' => array(
            'location' => realpath(__DIR__ . '/../data/uploads/')
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'form' => 'Common\Form\View\Helper\Form',
            'formCollection' => 'Common\Form\View\Helper\FormCollection',
            'formRow' => 'Common\Form\View\Helper\FormRow',
            'formElement' => 'Common\Form\View\Helper\FormElement',
            'formElementErrors' => 'Common\Form\View\Helper\FormElementErrors',
            'formErrors' => 'Common\Form\View\Helper\FormErrors',
            'version' => 'Common\View\Helper\Version',
            'applicationName' => 'Common\View\Helper\ApplicationName',
            'formPlainText'     => 'Common\Form\View\Helper\FormPlainText',
            'flashMessengerAll' => 'Common\View\Helper\FlashMessenger',
            'assetPath' => 'Common\View\Helper\AssetPath'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'partials/view' => __DIR__ . '/../view'
        )
    ),
    'local_scripts_path' => [__DIR__ . '/../src/Common/assets/js/inline/'],
    'forms_path' => __DIR__ .'/../../Common/src/Common/Form/Forms/',
    'form_elements' => [
        'invokables' => [
            'DateSelect' => 'Common\Form\Elements\Custom\DateSelect'
        ],
        'factories' => [
            'Common\Form\Element\DynamicSelect' => 'Common\Form\Element\DynamicSelectFactory'
        ],
        'aliases' => [
            'DynamicSelect' => 'Common\Form\Element\DynamicSelect'
        ]
    ],
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
            'document' => array(
                'Olcs\Template' => 'template',
                'Olcs\Document\GenerateRtf' => 'document/generate/rtf',
                'Olcs\Document\Retrieve' => 'document/retrieve/'
            )
        ),
        'endpoints' => array(
            'payments' => 'http://olcspayment.dev/api/',
            'backend' => 'http://olcs-backend/',
            'postcode' => 'http://dvsa-postcode.olcspv-ap01.olcs.npm/'
        )
    )
     //-------- End service API mappings -----------------
);
