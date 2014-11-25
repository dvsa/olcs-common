<?php

$release = json_decode(file_get_contents(__DIR__ . '/release.json'), true);

return array(
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
        'invokables' => array(
            'Common\Controller\File' => 'Common\Controller\FileController',
            'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
        )
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
        'shared' => array(
            'Helper\FileUpload' => false
        ),
        'abstract_factories' => array(
            'Common\Util\AbstractServiceFactory',
            'Common\Filter\Publication\Builder\PublicationBuilderAbstractFactory'
        ),
        'aliases' => array(
            'DataServiceManager' => 'Common\Service\Data\PluginManager',
            'translator' => 'MvcTranslator',
            'Zend\Log' => 'Logger',
            'ContentStore' => 'Dvsa\Jackrabbit\Service\Client',
        ),
        'invokables' => array(
            'Document' => '\Common\Service\Document\Document',
            'Common\Filesystem\Filesystem' => 'Common\Filesystem\Filesystem'
        ),
        'factories' => array(
            'Common\Service\Data\Sla' => 'Common\Service\Data\Sla',
            'Common\Service\Data\RefData' => 'Common\Service\Data\RefData',
            'Common\Service\Data\Country' => 'Common\Service\Data\Country',
            'Common\Service\Data\Licence' => 'Common\Service\Data\Licence',
            'Common\Service\Data\Publication' => 'Common\Service\Data\Publication',
            'Common\Service\Data\PublicationLink' => 'Common\Service\Data\PublicationLink',

            'OlcsCustomForm' => function ($sm) {
                    return new \Common\Service\Form\OlcsCustomFormFactory($sm->get('Config'));
            },
            'Script' => '\Common\Service\Script\ScriptFactory',
            'Table' => '\Common\Service\Table\TableFactory',
            'FileUploader' => '\Common\Service\File\FileUploaderFactory',
            'ServiceApiResolver' => 'Common\Service\Api\ResolverFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'SectionService' => '\Common\Controller\Service\SectionServiceFactory',
            'postcode' => function ($serviceManager) {
                $postcode = new \Common\Service\Postcode\Postcode();
                $postcode->setServiceLocator($serviceManager);
                return $postcode;
            },
            'vehicleList' => function ($serviceManager) {
                $vehicleList = new \Common\Service\VehicleList\VehicleList();
                $vehicleList->setServiceLocator($serviceManager);
                return $vehicleList;
            },
            'postcodeTrafficAreaValidator' => function ($serviceManager) {
                $validator = new \Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator();
                $validator->setServiceLocator($serviceManager);
                return $validator;
            },
            'goodsDiscStartNumberValidator' => function ($serviceManager) {
                return new \Common\Form\Elements\Validators\GoodsDiscStartNumberValidator();
            },
            'category' => '\Common\Service\Data\CategoryDataService',
            'country' => '\Common\Service\Data\Country',
            'staticList' => 'Common\Service\Data\StaticList',
            'FormAnnotationBuilder' => '\Common\Service\FormAnnotationBuilderFactory',
            'Common\Service\Data\PluginManager' => 'Common\Service\Data\PluginManagerFactory',
            'section.vehicle-safety.vehicle.formatter.vrm' => function ($serviceManager) {
                return new \Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm();
            },
            'FeeCommon' => 'Common\Service\Fee\FeeCommon',
            'Common\Util\DateTimeProcessor' => 'Common\Util\DateTimeProcessor'
        )
    ),
    'publications' => array(
        'HearingPublicationFilter' => array(
            'Common\Filter\Publication\Licence',
            'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\PublicationSection',
            'Common\Filter\Publication\PiVenue',
            'Common\Filter\Publication\HearingDateTime',
            'Common\Filter\Publication\PreviousPublication',
            'Common\Filter\Publication\PreviousHearing',
            'Common\Filter\Publication\HearingText1'
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
            'formPlainText' => 'Common\Form\View\Helper\FormPlainText',
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
    'forms_path' => __DIR__ . '/../../Common/src/Common/Form/Forms/',
    'form_elements' => [
        'invokables' => [
            'DateSelect' => 'Common\Form\Elements\Custom\DateSelect',
            'DateTimeSelect' => 'Common\Form\Elements\Custom\DateTimeSelect',
            'Common\Form\Elements\Custom\OlcsCheckbox' => 'Common\Form\Elements\Custom\OlcsCheckbox'
        ],
        'factories' => [
            'Common\Form\Element\DynamicSelect' => 'Common\Form\Element\DynamicSelectFactory',
            'Common\Form\Element\DynamicMultiSelect' => 'Common\Form\Element\DynamicMultiSelectFactory',
            'Common\Form\Element\DynamicMultiCheckbox' => 'Common\Form\Element\DynamicMultiCheckboxFactory',
            'Common\Form\Element\DynamicRadio' => 'Common\Form\Element\DynamicRadioFactory'
        ],
        'aliases' => [
            'DynamicSelect' => 'Common\Form\Element\DynamicSelect',
            'DynamicMultiSelect' => 'Common\Form\Element\DynamicMultiSelect',
            'DynamicMultiCheckbox' => 'Common\Form\Element\DynamicMultiCheckbox',
            'DynamicRadio' => 'Common\Form\Element\DynamicRadio',
            'OlcsCheckbox' => 'Common\Form\Elements\Custom\OlcsCheckbox'
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
            'Common\Filter\DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier',
            'Common\Filter\DecompressUploadToTmp' => 'Common\Filter\DecompressUploadToTmp',
            'Common\Filter\DecompressToTmp' => 'Common\Filter\DecompressToTmp',
        ],
        'delegators' => [
            'Common\Filter\DecompressUploadToTmp' => ['Common\Filter\DecompressToTmpDelegatorFactory'],
            'Common\Filter\DecompressToTmp' => ['Common\Filter\DecompressToTmpDelegatorFactory']
        ],
        'aliases' => [
            'DateSelectNullifier' => 'Common\Filter\DateSelectNullifier',
            'DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier',
            'DecompressUploadToTmp' => 'Common\Filter\DecompressUploadToTmp',
            'DecompressToTmp' => 'Common\Filter\DecompressToTmp'
        ]
    ],
    'data_services' => [
        'factories' => [
            'Common\Service\Data\PublicHoliday' => 'Common\Service\Data\PublicHoliday',
            'Common\Service\Data\PiVenue' => 'Common\Service\Data\PiVenue',
            'Common\Service\Data\PiHearing' => 'Common\Service\Data\PiHearing',
        ]
    ],
    'tables' => array(
        'config' => array(
            __DIR__ . '/../src/Common/Table/Tables/'
        ),
        'partials' => __DIR__ . '/../view/table/'
    ),
    'sic_codes_path' => __DIR__ . '/../../Common/config/sic-codes',
    'fieldsets_path' => __DIR__ . '/../../Common/src/Common/Form/Fieldsets/',
    'static-list-data' => include __DIR__ . '/list-data/static-list-data.php',
    'form' => array(
        'elements' => include __DIR__ . '/../src/Common/Form/Elements/getElements.php'
    ),
    'rest_services' => [
        'abstract_factories' => [
            'Common\Service\Api\AbstractFactory'
        ]
    ],
    'service_api_mapping' => array(
        'endpoints' => array(
            'payments' => 'http://olcspayment.dev/api/',
            'backend' => 'http://olcs-backend/',
            'postcode' => 'http://dvsa-postcode.olcspv-ap01.olcs.npm/'
        )
    )
);
