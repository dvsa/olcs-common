<?php

list($allRoutes, $controllers, $journeys) = include(__DIR__ . '/journeys.config.php');

$release = json_decode(file_get_contents(__DIR__ . '/release.json'), true);

$invokeables = array_merge(
    $controllers, array(
        'Common\Controller\File' => 'Common\Controller\FileController',
        'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
    )
);

return array(
    'journeys' => $journeys,
    'router' => array(
        'routes' => array(
            'application_start' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/application_start_page'
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
        'invokables' => array(
            'DataServiceManager' => 'Common\Service\Data\PluginManager'
        ),
        'services' => array(
            'address' => new \Common\Service\Address\Address()
        ),
        'factories' => array(
            'SectionService' => '\Common\Controller\Service\SectionServiceFactory',
            'HelperService' => '\Common\Service\Helper\HelperServiceFactory',
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
            'postcodePhlTrafficAreaValidator' => function ($serviceManager) {
                $validator = new \Common\Form\Elements\Validators\PrivateHireLicenceTrafficAreaValidator();
                $validator->setServiceLocator($serviceManager);
                return $validator;
            },
            'goodsDiscStartNumberValidator' => function ($serviceManager) {
                return new \Common\Form\Elements\Validators\GoodsDiscStartNumberValidator();
            },
            'licence' => function ($serviceManager) {
                $licenceService = new \Common\Service\Licence\Licence();
                $licenceService->setServiceLocator($serviceManager);
                return $licenceService;
            },
            'category' => '\Common\Service\Data\CategoryData',
            'country' => '\Common\Service\Data\Country',
            'staticList' => 'Common\Service\Data\StaticList',
            'FormAnnotationBuilder' => '\Common\Service\FormAnnotationBuilderFactory',
            'section.vehicle-safety.vehicle.formatter.vrm' => function ($serviceManager) {
                return new \Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm();
            }
        )
    ),
    'file_uploader' => array(
        'default' => 'ContentStore',
        'config' => array(
            'location' => 'documents'
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
            'formDateTimeSelect' => 'Common\Form\View\Helper\FormDateTimeSelect',
            'version' => 'Common\View\Helper\Version',
            'applicationName' => 'Common\View\Helper\ApplicationName',
            'formPlainText'     => 'Common\Form\View\Helper\FormPlainText',
            'flashMessengerAll' => 'Common\View\Helper\FlashMessenger',
            'assetPath' => 'Common\View\Helper\AssetPath',
            'addTags' => 'Common\View\Helper\AddTags'
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
            'DateSelect' => 'Common\Form\Elements\Custom\DateSelect',
            'DateTimeSelect' => 'Common\Form\Elements\Custom\DateTimeSelect'
        ],
        'factories' => [
            'Common\Form\Element\DynamicSelect' => 'Common\Form\Element\DynamicSelectFactory',
            'Common\Form\Element\DynamicMultiCheckbox' => 'Common\Form\Element\DynamicMultiCheckboxFactory',
            'Common\Form\Element\DynamicRadio' => 'Common\Form\Element\DynamicRadioFactory'
        ],
        'aliases' => [
            'DynamicSelect' => 'Common\Form\Element\DynamicSelect',
            'DynamicMultiCheckbox' => 'Common\Form\Element\DynamicMultiCheckbox',
            'DynamicRadio' => 'Common\Form\Element\DynamicRadio',
        ]
    ],
    'validators' => [
        'invokables' => [
            'Common\Validator\ValidateIf' => 'Common\Validator\ValidateIf',
            'Common\Validator\DateCompare' => 'Common\Validator\DateCompare',
            'Common\Form\Elements\Validators\DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'Common\Validator\OneOf' => 'Common\Validator\OneOf',
        ],
        'aliases' => [
            'ValidateIf' => 'Common\Validator\ValidateIf',
            'DateCompare' => 'Common\Validator\DateCompare',
            'DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'OneOf' => 'Common\Validator\OneOf'
        ]
    ],
    'filters' => [
        'invokables' => [
            'Common\Filter\DateSelectNullifier' => 'Common\Filter\DateSelectNullifier',
            'Common\Filter\DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier'
        ],
        'aliases' => [
            'DateSelectNullifier' => 'Common\Filter\DateSelectNullifier',
            'DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier'
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
