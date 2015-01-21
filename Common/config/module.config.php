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
        // @NOTE These delegators can live in common as both internal and external app controllers currently use the
        // same adapter
        'delegators' => array(
            'LvaApplication/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\ApplicationTypeOfLicenceDelegator'
            ),
            'LvaLicence/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\LicenceTypeOfLicenceDelegator'
            ),
            'LvaVariation/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\VariationTypeOfLicenceDelegator'
            ),
            'LvaApplication/Vehicles' => array(
                'Common\Controller\Lva\Delegators\ApplicationVehicleGoodsDelegator'
            ),
            'LvaLicence/OperatingCentres' => array(
                'Common\Controller\Lva\Delegators\LicenceOperatingCentreDelegator'
            ),
            'LvaVariation/OperatingCentres' => array(
                'Common\Controller\Lva\Delegators\VariationOperatingCentreDelegator'
            ),
            'LvaApplication/OperatingCentres' => array(
                'Common\Controller\Lva\Delegators\ApplicationOperatingCentreDelegator'
            ),
        ),
        'abstract_factories' => array(
            'Common\Controller\Lva\AbstractControllerFactory',
        ),
        'invokables' => array(
            'Common\Controller\File' => 'Common\Controller\FileController',
            'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'redirect' => 'Common\Controller\Plugin\Redirect',
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
            'Helper\FileUpload' => false,
            'CantIncreaseValidator' => false
        ),
        'abstract_factories' => array(
            'Common\Util\AbstractServiceFactory',
            'Common\Filter\Publication\Builder\PublicationBuilderAbstractFactory'
        ),
        'aliases' => array(
            'DataServiceManager' => 'Common\Service\Data\PluginManager',
            'BundleManager' => 'Common\Service\Data\BundleManager',
            'translator' => 'MvcTranslator',
            'Zend\Log' => 'Logger',
            'ContentStore' => 'Dvsa\Jackrabbit\Service\Client',
        ),
        'invokables' => array(
            'CantIncreaseValidator' => 'Common\Form\Elements\Validators\CantIncreaseValidator',
            'ApplicationTypeOfLicenceAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationTypeOfLicenceAdapter',
            'ApplicationVehicleGoodsAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationVehicleGoodsAdapter',
            'LicenceTypeOfLicenceAdapter'
                => 'Common\Controller\Lva\Adapters\LicenceTypeOfLicenceAdapter',
            'VariationTypeOfLicenceAdapter'
                => 'Common\Controller\Lva\Adapters\VariationTypeOfLicenceAdapter',
            'LicenceOperatingCentreAdapter'
                => 'Common\Controller\Lva\Adapters\LicenceOperatingCentreAdapter',
            'VariationOperatingCentreAdapter'
                => 'Common\Controller\Lva\Adapters\VariationOperatingCentreAdapter',
            'ApplicationOperatingCentreAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationOperatingCentreAdapter',
            'Document' => '\Common\Service\Document\Document',
            'Common\Filesystem\Filesystem' => 'Common\Filesystem\Filesystem',
            'VehicleList' => '\Common\Service\VehicleList\VehicleList',
            'PrintScheduler' => '\Common\Service\Printing\DocumentStubPrintScheduler',
            'postcode' => 'Common\Service\Postcode\Postcode',
            'postcodeTrafficAreaValidator' => 'Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator',
            'goodsDiscStartNumberValidator' => 'Common\Form\Elements\Validators\GoodsDiscStartNumberValidator',
            'applicationIdValidator' => 'Common\Form\Elements\Validators\ApplicationIdValidator',
            'oneRowInTablesRequired' => 'Common\Form\Elements\Validators\Lva\OneRowInTablesRequiredValidator',
            'section.vehicle-safety.vehicle.formatter.vrm' =>
                'Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm'
        ),
        'factories' => array(
            'ApplicationLvaAdapter' => 'Common\Controller\Lva\Factories\ApplicationLvaAdapterFactory',
            'LicenceLvaAdapter' => 'Common\Controller\Lva\Factories\LicenceLvaAdapterFactory',
            'VariationLvaAdapter' => 'Common\Controller\Lva\Factories\VariationLvaAdapterFactory',
            'Common\Service\Data\Sla' => 'Common\Service\Data\Sla',
            'Common\Service\Data\RefData' => 'Common\Service\Data\RefData',
            'Common\Service\Data\Country' => 'Common\Service\Data\Country',
            'Common\Service\Data\Licence' => 'Common\Service\Data\Licence',
            'Common\Service\Data\Publication' => 'Common\Service\Data\Publication',
            'Common\Service\ShortNotice' => 'Common\Service\ShortNotice',

            'OlcsCustomForm' => function ($sm) {
                    return new \Common\Service\Form\OlcsCustomFormFactory($sm->get('Config'));
            },
            'Script' => '\Common\Service\Script\ScriptFactory',
            'Table' => '\Common\Service\Table\TableFactory',
            'FileUploader' => '\Common\Service\File\FileUploaderFactory',
            'ServiceApiResolver' => 'Common\Service\Api\ResolverFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'SectionService' => '\Common\Controller\Service\SectionServiceFactory',
            'category' => '\Common\Service\Data\CategoryDataService',
            'country' => '\Common\Service\Data\Country',
            'staticList' => 'Common\Service\Data\StaticList',
            'FormAnnotationBuilder' => '\Common\Service\FormAnnotationBuilderFactory',
            'Common\Service\Data\PluginManager' => 'Common\Service\Data\PluginManagerFactory',
            'Common\Service\Data\BundleManager' => 'Common\Service\Data\BundleManagerFactory',
            'Common\Util\DateTimeProcessor' => 'Common\Util\DateTimeProcessor',
            'Cpms\IdentityProvider' => 'Common\Service\Cpms\IdentityProviderFactory'
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
            'Common\Filter\Publication\PreviousUnpublished',
            'Common\Filter\Publication\HearingText1',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
        'DecisionPublicationFilter' => array(
            'Common\Filter\Publication\LastHearing',
            'Common\Filter\Publication\Licence',
            'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\PublicationSection',
            'Common\Filter\Publication\PiVenue',
            'Common\Filter\Publication\HearingDateTime',
            'Common\Filter\Publication\PreviousPublication',
            'Common\Filter\Publication\PreviousUnpublished',
            'Common\Filter\Publication\DecisionText1',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
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
            'addTags' => 'Common\View\Helper\AddTags',
            'readonlyformitem' => 'Common\Form\View\Helper\Readonly\FormItem',
            'readonlyformselect' => 'Common\Form\View\Helper\Readonly\FormSelect',
            'readonlyformdateselect' => 'Common\Form\View\Helper\Readonly\FormDateSelect',
            'readonlyformrow' => 'Common\Form\View\Helper\Readonly\FormRow'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'partials/view' => __DIR__ . '/../view',
            'translations' => __DIR__ . '/../config/language/partials'
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
            'Common\Form\Element\DynamicMultiCheckbox' => 'Common\Form\Element\DynamicMultiCheckboxFactory',
            'Common\Form\Element\DynamicRadio' => 'Common\Form\Element\DynamicRadioFactory'
        ],
        'aliases' => [
            'DynamicSelect' => 'Common\Form\Element\DynamicSelect',
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
            'Common\Form\Elements\Validators\Date' => 'Common\Form\Elements\Validators\Date'
        ],
        'aliases' => [
            'ValidateIf' => 'Common\Validator\ValidateIf',
            'DateCompare' => 'Common\Validator\DateCompare',
            'DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'OneOf' => 'Common\Validator\OneOf',
            'Date' => 'Common\Form\Elements\Validators\Date'
        ]
    ],
    'filters' => [
        'invokables' => [
            'Common\Filter\DateSelectNullifier' => 'Common\Filter\DateSelectNullifier',
            'Common\Filter\DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier',
            'Common\Filter\DecompressUploadToTmp' => 'Common\Filter\DecompressUploadToTmp',
            'Common\Filter\DecompressToTmp' => 'Common\Filter\DecompressToTmp',
            'Common\Filter\Publication\Licence' => 'Common\Filter\Publication\Licence',
            'Common\Filter\Publication\LicenceAddress' => 'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication' => 'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\PublicationSection' => 'Common\Filter\Publication\PublicationSection',
            'Common\Filter\Publication\PiVenue' => 'Common\Filter\Publication\PiVenue',
            'Common\Filter\Publication\HearingDateTime' => 'Common\Filter\Publication\HearingDateTime',
            'Common\Filter\Publication\PreviousPublication' => 'Common\Filter\Publication\PreviousPublication',
            'Common\Filter\Publication\PreviousHearing' => 'Common\Filter\Publication\PreviousHearing',
            'Common\Filter\Publication\PreviousUnpublished' => 'Common\Filter\Publication\PreviousUnpublished',
            'Common\Filter\Publication\HearingText1' => 'Common\Filter\Publication\HearingText1',
            'Common\Filter\Publication\PoliceData' => 'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean' => 'Common\Filter\Publication\Clean'
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
        'abstract_factories' => [
            'Common\Service\Data\DataServiceAbstractFactory'
        ],
        'factories' => [
            'Common\Service\Data\PublicHoliday' => 'Common\Service\Data\PublicHoliday',
            'Common\Service\Data\PiVenue' => 'Common\Service\Data\PiVenue',
            'Common\Service\Data\PiHearing' => 'Common\Service\Data\PiHearing',
            'Common\Service\Data\PublicationLink' => 'Common\Service\Data\PublicationLink',
            'Common\Service\Data\LicenceOperatingCentre' =>
                'Common\Service\Data\LicenceOperatingCentre',
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
    ),
    'caches'=> array(
        'array'=> array(
            'adapter' => array(
                'name' => 'memory',
                'lifetime' => 0,
            ),
        )
    ),
);
