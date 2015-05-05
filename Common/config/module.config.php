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
                    'route' => '/file/:file[/:namespace]/:name',
                    'defaults' => array(
                        'controller' => 'Common\Controller\File',
                        'action' => 'download'
                    )
                )
            ),
            'transport_manager_review' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/transport-manager-application/review/:id[/]',
                    'defaults' => array(
                        'controller' => 'TransportManagerReview',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'initializers' => array(
            'Common\Controller\Crud\Initializer'
        ),
        // @NOTE These delegators can live in common as both internal and external app controllers currently use the
        // same adapter
        'delegators' => array(
            'LvaApplication/Review' => array(
                'Common\Controller\Lva\Delegators\ApplicationReviewDelegator'
            ),
            'LvaVariation/Review' => array(
                'Common\Controller\Lva\Delegators\VariationReviewDelegator'
            ),
            'LvaApplication/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\ApplicationTypeOfLicenceDelegator'
            ),
            'LvaLicence/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\LicenceTypeOfLicenceDelegator'
            ),
            'LvaVariation/TypeOfLicence' => array(
                'Common\Controller\Lva\Delegators\VariationTypeOfLicenceDelegator'
            ),
            'LvaApplication/BusinessType' => array(
                // @NOTE: we need an associative array when we need to override the
                // delegator elsewhere, such as in selfserve or internal
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ),
            'LvaLicence/BusinessType' => array(
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ),
            'LvaVariation/BusinessType' => array(
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ),
            'LvaApplication/Vehicles' => array(
                'Common\Controller\Lva\Delegators\ApplicationVehiclesGoodsDelegator'
            ),
            'LvaLicence/Vehicles' => array(
                'Common\Controller\Lva\Delegators\LicenceVehiclesGoodsDelegator'
            ),
            'LvaVariation/Vehicles' => array(
                'Common\Controller\Lva\Delegators\VariationVehiclesGoodsDelegator'
            ),
            'LvaApplication/VehiclesPsv' => array(
                'Common\Controller\Lva\Delegators\ApplicationVehiclesPsvDelegator'
            ),
            'LvaLicence/VehiclesPsv' => array(
                'Common\Controller\Lva\Delegators\LicenceVehiclesPsvDelegator'
            ),
            'LvaVariation/VehiclesPsv' => array(
                'Common\Controller\Lva\Delegators\VariationVehiclesPsvDelegator'
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
            'LvaApplication/CommunityLicences' => array(
                'Common\Controller\Lva\Delegators\ApplicationCommunityLicenceDelegator'
            ),
            'LvaVariation/CommunityLicences' => array(
                'Common\Controller\Lva\Delegators\VariationCommunityLicenceDelegator'
            ),
            'LvaLicence/CommunityLicences' => array(
                'Common\Controller\Lva\Delegators\LicenceCommunityLicenceDelegator'
            ),
            'LvaApplication/FinancialEvidence' => array(
                'Common\Controller\Lva\Delegators\ApplicationFinancialEvidenceDelegator'
            ),
            'LvaVariation/FinancialEvidence' => array(
                'Common\Controller\Lva\Delegators\VariationFinancialEvidenceDelegator'
            ),
            'LvaLicence/People' => array(
                'Common\Controller\Lva\Delegators\LicencePeopleDelegator'
            ),
            'LvaVariation/People' => array(
                'Common\Controller\Lva\Delegators\VariationPeopleDelegator'
            ),
            'LvaApplication/People' => array(
                'Common\Controller\Lva\Delegators\ApplicationPeopleDelegator'
            ),
            'LvaLicence/TransportManagers' => array(
                'Common\Controller\Lva\Delegators\LicenceTransportManagerDelegator'
            ),
            'LvaVariation/TransportManagers' => array(
                'Common\Controller\Lva\Delegators\VariationTransportManagerDelegator'
            ),
            'LvaApplication/TransportManagers' => array(
                'Common\Controller\Lva\Delegators\ApplicationTransportManagerDelegator'
            ),
        ),
        'abstract_factories' => array(
            'Common\Controller\Lva\AbstractControllerFactory',
        ),
        'invokables' => array(
            'GenericCrudController' => 'Common\Controller\Crud\GenericCrudController',
            'Common\Controller\File' => 'Common\Controller\FileController',
            'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
            'TransportManagerReview' => 'Common\Controller\TransportManagerReviewController',
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'redirect' => 'Common\Controller\Plugin\Redirect',
        ),
        'factories' => [
            'currentUser' => \Common\Controller\Plugin\CurrentUserFactory::class,
			'ElasticSearch' => 'Common\Controller\Plugin\ElasticSearchFactory',
        ]
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
        'delegators' => [
            'zfcuser_user_mapper' => [
                \Common\Rbac\UserProviderDelegatorFactory::class
            ]
        ],
        'shared' => array(
            'Helper\FileUpload' => false,
            'CantIncreaseValidator' => false
        ),
        'abstract_factories' => array(
            'Common\Util\AbstractServiceFactory',
            'Common\Filter\Publication\Builder\PublicationBuilderAbstractFactory',
        ),
        'aliases' => array(
            'Cache' => 'Zend\Cache\Storage\StorageInterface',
            'DataServiceManager' => 'Common\Service\Data\PluginManager',
            'BundleManager' => 'Common\Service\Data\BundleManager',
            'translator' => 'MvcTranslator',
            'Zend\Log' => 'Logger',
            'ContentStore' => 'Dvsa\Jackrabbit\Service\Client',
            'TableBuilder' => 'Common\Service\Table\TableBuilderFactory',
        ),
        'invokables' => array(
            'CrudListener' => 'Common\Controller\Crud\Listener',
            'SectionConfig' => 'Common\Service\Data\SectionConfig',
            'ApplicationReviewAdapter' => 'Common\Controller\Lva\Adapters\ApplicationReviewAdapter',
            'VariationReviewAdapter' => 'Common\Controller\Lva\Adapters\VariationReviewAdapter',
            'CantIncreaseValidator' => 'Common\Form\Elements\Validators\CantIncreaseValidator',
            'GenericBusinessTypeAdapter'
                => 'Common\Controller\Lva\Adapters\GenericBusinessTypeAdapter',
            'ApplicationConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationConditionsUndertakingsAdapter',
            'VariationConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter',
            'LicenceConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\LicenceConditionsUndertakingsAdapter',
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
            'VariationFinancialEvidenceAdapter'
                => 'Common\Controller\Lva\Adapters\VariationFinancialEvidenceAdapter',
            'ApplicationFinancialEvidenceAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationFinancialEvidenceAdapter',
            'ApplicationVehiclesGoodsAdapter' => 'Common\Controller\Lva\Adapters\ApplicationVehiclesGoodsAdapter',
            'LicenceVehiclesGoodsAdapter' => 'Common\Controller\Lva\Adapters\LicenceVehiclesGoodsAdapter',
            'VariationVehiclesGoodsAdapter' => 'Common\Controller\Lva\Adapters\VariationVehiclesGoodsAdapter',
            'ApplicationVehiclesPsvAdapter' => 'Common\Controller\Lva\Adapters\ApplicationVehiclesPsvAdapter',
            'LicenceVehiclesPsvAdapter' => 'Common\Controller\Lva\Adapters\LicenceVehiclesPsvAdapter',
            'VariationVehiclesPsvAdapter' => 'Common\Controller\Lva\Adapters\VariationVehiclesPsvAdapter',
            'ApplicationCommunityLicenceAdapter' =>
                'Common\Controller\Lva\Adapters\ApplicationCommunityLicenceAdapter',
            'VariationCommunityLicenceAdapter' =>
                'Common\Controller\Lva\Adapters\VariationCommunityLicenceAdapter',
            'LicenceCommunityLicenceAdapter' =>
                'Common\Controller\Lva\Adapters\LicenceCommunityLicenceAdapter',
            'ApplicationPeopleAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationPeopleAdapter',
            'LicencePeopleAdapter'
                => 'Common\Controller\Lva\Adapters\LicencePeopleAdapter',
            'VariationPeopleAdapter'
                => 'Common\Controller\Lva\Adapters\VariationPeopleAdapter',
            'Document' => '\Common\Service\Document\Document',
            'Common\Filesystem\Filesystem' => 'Common\Filesystem\Filesystem',
            'VehicleList' => '\Common\Service\VehicleList\VehicleList',
            'PrintScheduler' => '\Common\Service\Printing\DocumentStubPrintScheduler',
            'postcode' => 'Common\Service\Postcode\Postcode',
            'email' => 'Common\Service\Email\Email',
            'postcodeTrafficAreaValidator' => 'Common\Form\Elements\Validators\OperatingCentreTrafficAreaValidator',
            'goodsDiscStartNumberValidator' => 'Common\Form\Elements\Validators\GoodsDiscStartNumberValidator',
            'applicationIdValidator' => 'Common\Form\Elements\Validators\ApplicationIdValidator',
            'oneRowInTablesRequired' => 'Common\Form\Elements\Validators\Lva\OneRowInTablesRequiredValidator',
            'totalVehicleAuthorityValidator' => 'Common\Form\Elements\Validators\Lva\TotalVehicleAuthorityValidator',
            'section.vehicle-safety.vehicle.formatter.vrm' =>
                'Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm',
            'Common\Rbac\UserProvider' => 'Common\Rbac\UserProvider',

            'LicenceTransportManagerAdapter'
                => 'Common\Controller\Lva\Adapters\LicenceTransportManagerAdapter',
            'VariationTransportManagerAdapter'
                => 'Common\Controller\Lva\Adapters\VariationTransportManagerAdapter',
            'ApplicationTransportManagerAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationTransportManagerAdapter',
            'DataMapper\DashboardTmApplications' => 'Common\Service\Table\DataMapper\DashboardTmApplications',
        ),
        'factories' => array(
            'CrudServiceManager' => 'Common\Service\Crud\CrudServiceManagerFactory',
            'FormServiceManager' => 'Common\FormService\FormServiceManagerFactory',
            'BusinessServiceManager' => 'Common\BusinessService\BusinessServiceManagerFactory',
            'BusinessRuleManager' => 'Common\BusinessRule\BusinessRuleManagerFactory',
            'ApplicationLvaAdapter' => 'Common\Controller\Lva\Factories\ApplicationLvaAdapterFactory',
            'LicenceLvaAdapter' => 'Common\Controller\Lva\Factories\LicenceLvaAdapterFactory',
            'VariationLvaAdapter' => 'Common\Controller\Lva\Factories\VariationLvaAdapterFactory',
            'Common\Service\Data\Sla' => 'Common\Service\Data\Sla',
            'Common\Service\Data\RefData' => 'Common\Service\Data\RefData',
            'Common\Service\Data\Country' => 'Common\Service\Data\Country',
            'Common\Service\Data\Licence' => 'Common\Service\Data\Licence',
            'Common\Service\Data\Application' => 'Common\Service\Data\Application',
            'Common\Service\Data\Publication' => 'Common\Service\Data\Publication',
            'Common\Service\Data\LicenceOperatingCentre' => 'Common\Service\Data\LicenceOperatingCentre',
            'Common\Service\Data\ApplicationOperatingCentre' => 'Common\Service\Data\ApplicationOperatingCentre',
            'Common\Service\ShortNotice' => 'Common\Service\ShortNotice',
            'Common\Service\Data\EbsrSubTypeListDataService' => 'Common\Service\Data\EbsrSubTypeListDataService',
            'Common\Service\Data\UserTypesListDataService' => 'Common\Service\Data\UserTypesListDataService',
            'Script' => '\Common\Service\Script\ScriptFactory',
            'Table' => '\Common\Service\Table\TableFactory',
            // Added in a true Zend Framework V2 compatible factory for TableBuilder, eventually to replace Table above.
            'Common\Service\Table\TableBuilderFactory' => 'Common\Service\Table\TableBuilderFactory',
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
            'Cpms\IdentityProvider' => 'Common\Service\Cpms\IdentityProviderFactory',
            'Zend\Cache\Storage\StorageInterface' => 'Zend\Cache\Service\StorageCacheFactory',
            'Common\Rbac\Navigation\IsAllowedListener' => 'Common\Rbac\Navigation\IsAllowedListener',
            \Common\Service\Data\Search\SearchTypeManager::class =>
                \Common\Service\Data\Search\SearchTypeManagerFactory::class,
            'Common\Service\Data\Role' => 'Common\Service\Data\Role',
            'Common\Service\Data\Team' => 'Common\Service\Data\Team',
            'Common\Service\Data\HintQuestion' => 'Common\Service\Data\HintQuestion',
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
        'TmDecisionPublicationFilter' => array(
            'Common\Filter\Publication\LastHearing',
            'Common\Filter\Publication\TransportManager',
            'Common\Filter\Publication\PiVenue',
            'Common\Filter\Publication\HearingDateTime',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\PublicationSection',
            'Common\Filter\Publication\PreviousPublication',
            'Common\Filter\Publication\PreviousUnpublished',
            'Common\Filter\Publication\TmDecisionText1',
            'Common\Filter\Publication\TmDecisionText2',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
        'TmHearingPublicationFilter' => array(
            'Common\Filter\Publication\LastHearing',
            'Common\Filter\Publication\TransportManager',
            'Common\Filter\Publication\PiVenue',
            'Common\Filter\Publication\HearingDateTime',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\PublicationSection',
            'Common\Filter\Publication\PreviousPublication',
            'Common\Filter\Publication\PreviousUnpublished',
            'Common\Filter\Publication\TmHearingText1',
            'Common\Filter\Publication\TmHearingText2',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
        'BusRegGrantNewPublicationFilter' => array(
            'Common\Filter\Publication\BusRegLicence',
            'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\BusReg',
            'Common\Filter\Publication\BusRegPublicationSection',
            'Common\Filter\Publication\PreviousUnpublishedBus',
            'Common\Filter\Publication\BusRegServiceDesignation',
            'Common\Filter\Publication\BusRegServiceTypes',
            'Common\Filter\Publication\BusRegText1',
            'Common\Filter\Publication\BusRegText2',
            'Common\Filter\Publication\BusRegGrantNewText3',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
        'BusRegGrantVarPublicationFilter' => array(
            'Common\Filter\Publication\BusRegLicence',
            'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\BusReg',
            'Common\Filter\Publication\BusRegPublicationSection',
            'Common\Filter\Publication\PreviousUnpublishedBus',
            'Common\Filter\Publication\BusRegServiceDesignation',
            'Common\Filter\Publication\BusRegVarReason',
            'Common\Filter\Publication\BusRegText1',
            'Common\Filter\Publication\BusRegText2',
            'Common\Filter\Publication\BusRegGrantVarText3',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
        'BusRegGrantCancelPublicationFilter' => array(
            'Common\Filter\Publication\BusRegLicence',
            'Common\Filter\Publication\LicenceAddress',
            'Common\Filter\Publication\Publication',
            'Common\Filter\Publication\BusReg',
            'Common\Filter\Publication\BusRegPublicationSection',
            'Common\Filter\Publication\PreviousUnpublishedBus',
            'Common\Filter\Publication\BusRegServiceDesignation',
            'Common\Filter\Publication\BusRegText1',
            'Common\Filter\Publication\BusRegText2',
            'Common\Filter\Publication\BusRegGrantCancelText3',
            'Common\Filter\Publication\PoliceData',
            'Common\Filter\Publication\Clean'
        ),
    ),
    'file_uploader' => array(
        'default' => 'ContentStore',
        'config' => array(
            'location' => 'documents',
            'defaultPath' => '[locale]/[doc_type_name]/[year]/[month]', // e.g. gb/publications/2015/03
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
            'readonlyformrow' => 'Common\Form\View\Helper\Readonly\FormRow',
            'readonlyformtable' => 'Common\Form\View\Helper\Readonly\FormTable',
            'currentUser' => 'Common\View\Helper\CurrentUser',
            'transportManagerApplicationStatus' => 'Common\View\Helper\TransportManagerApplicationStatus',
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
            'MonthSelect' => 'Common\Form\Elements\Custom\MonthSelect',
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
            'Common\Service\Data\LicenceListDataService' => 'Common\Service\Data\LicenceListDataService',
            'Common\Service\Data\LicenceOperatingCentre' =>
                'Common\Service\Data\LicenceOperatingCentre',
            'Common\Service\Data\ApplicationOperatingCentre' =>
                'Common\Service\Data\ApplicationOperatingCentre',
            'Common\Service\Data\OcContextListDataService' => 'Common\Service\Data\OcContextListDataService'

        ]
    ],
    'tables' => array(
        'config' => array(
            __DIR__ . '/../src/Common/Table/Tables/'
        ),
        'partials' => array(
            'html' => __DIR__ . '/../view/table/',
            'csv' => __DIR__ . '/../view/table/csv'
        )
    ),
    'sic_codes_path' => __DIR__ . '/../../Common/config/sic-codes',
    'fieldsets_path' => __DIR__ . '/../../Common/src/Common/Form/Fieldsets/',
    'static-list-data' => include __DIR__ . '/list-data/static-list-data.php',
    // @todo I *think* we can remove this now
    'form' => array(),
    'rest_services' => [
        'abstract_factories' => [
            'Common\Service\Api\AbstractFactory'
        ]
    ],
    'service_api_mapping' => array(
        'endpoints' => array(
            'payments' => 'http://olcspayment.dev/api/',
            'backend' => 'http://olcs-backend/',
            'postcode' => 'http://dvsa-postcode.olcspv-ap01.olcs.npm/',
            'email' => 'http://olcs-email/',
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
    'zfc_rbac' => [
        'role_provider' => ['Common\Rbac\Role\RoleProvider' => []],
        'role_provider_manager' => [
            'factories' => [
                'Common\Rbac\Role\RoleProvider' => 'Common\Rbac\Role\RoleProvider'
            ]
        ],
        'protection_policy' => \ZfcRbac\Guard\GuardInterface::POLICY_DENY,
        'redirect_strategy' => [
            'redirect_when_connected'        => false,
            'redirect_to_route_disconnected' => 'zfcuser/login',
            'append_previous_uri'            => true,
            'previous_uri_query_key'         => 'redirectTo'
        ],
    ],
    'cache' => [
        'adapter' => [
            'name' => 'apc',
        ]
    ],
    'zfcuser' => [
        'auth_identity_fields' => array('username')
    ],
    'form_service_manager' => [
        'invokables' => [
            // Lva form services
            'lva-licence' => 'Common\FormService\Form\Lva\Licence',
            'lva-variation' => 'Common\FormService\Form\Lva\Variation',
            'lva-application' => 'Common\FormService\Form\Lva\Application',
            // Business details form services
            'lva-licence-business_details' => 'Common\FormService\Form\Lva\LicenceBusinessDetails',
            'lva-variation-business_details' => 'Common\FormService\Form\Lva\VariationBusinessDetails',
            'lva-application-business_details' => 'Common\FormService\Form\Lva\ApplicationBusinessDetails',
            // Addresses form services
            'lva-licence-addresses' => 'Common\FormService\Form\Lva\Addresses',
            'lva-variation-addresses' => 'Common\FormService\Form\Lva\Addresses',
            'lva-application-addresses' => 'Common\FormService\Form\Lva\Addresses',
            // Goods vehicle form services
            'lva-licence-goods-vehicles' => 'Common\FormService\Form\Lva\LicenceGoodsVehicles',
            'lva-variation-goods-vehicles' => 'Common\FormService\Form\Lva\VariationGoodsVehicles',
            'lva-application-goods-vehicles' => 'Common\FormService\Form\Lva\ApplicationGoodsVehicles',
            // Goods vehicles vehicle form services
            'lva-licence-goods-vehicles-vehicle' => 'Common\FormService\Form\Lva\LicenceGoodsVehiclesVehicle',
            'lva-variation-goods-vehicles-vehicle' => 'Common\FormService\Form\Lva\VariationGoodsVehiclesVehicle',
            'lva-application-goods-vehicles-vehicle' => 'Common\FormService\Form\Lva\ApplicationGoodsVehiclesVehicle',
            // Psv vehicles vehicle form services
            'lva-licence-vehicles_psv-vehicle' => 'Common\FormService\Form\Lva\LicencePsvVehiclesVehicle',
            'lva-variation-vehicles_psv-vehicle' => 'Common\FormService\Form\Lva\VariationPsvVehiclesVehicle',
            'lva-application-vehicles_psv-vehicle' => 'Common\FormService\Form\Lva\ApplicationPsvVehiclesVehicle',
            // Goods vehicle filter form services
            'lva-licence-goods-vehicles-filters' => 'Common\FormService\Form\Lva\LicenceGoodsVehiclesFilters',
            'lva-variation-goods-vehicles-filters' => 'Common\FormService\Form\Lva\CommonGoodsVehiclesFilters',
            'lva-application-goods-vehicles-filters' => 'Common\FormService\Form\Lva\CommonGoodsVehiclesFilters',
            // Common vehicle services
            'lva-licence-variation-vehicles' => 'Common\FormService\Form\Lva\LicenceVariationVehicles',
            'lva-generic-vehicles-vehicle' => 'Common\FormService\Form\Lva\GenericVehiclesVehicle',
        ]
    ],
    'business_rule_manager' => [
        'invokables' => [
            'Task' => 'Common\BusinessRule\Rule\Task',
            'Fee' => 'Common\BusinessRule\Rule\Fee',
            'TradingNames' => 'Common\BusinessRule\Rule\TradingNames',
            'BusinessDetails' => 'Common\BusinessRule\Rule\BusinessDetails',
            'CheckDate' => 'Common\BusinessRule\Rule\CheckDate',
            'GoodsVehiclesVehicle' => 'Common\BusinessRule\Rule\GoodsVehiclesVehicle',
            'LicenceGoodsVehiclesLicenceVehicle' => 'Common\BusinessRule\Rule\LicenceGoodsVehiclesLicenceVehicle',
            'LicenceGoodsVehiclesRemovedVehicle' => 'Common\BusinessRule\Rule\LicenceGoodsVehiclesRemovedVehicle',
            'VariationGoodsVehiclesLicenceVehicle' => 'Common\BusinessRule\Rule\VariationGoodsVehiclesLicenceVehicle',
            'ApplicationGoodsVehiclesLicenceVehicle'
                => 'Common\BusinessRule\Rule\ApplicationGoodsVehiclesLicenceVehicle',
            'LockedDate' => 'Common\BusinessRule\Rule\LockedDate',
            'PhoneContacts' => 'Common\BusinessRule\Rule\PhoneContacts',
            'BirthDate' => 'Common\BusinessRule\Rule\BirthDate',
            'EnvironmentalComplaint' => 'Common\BusinessRule\Rule\EnvironmentalComplaint',
        ]
    ],
    'business_service_manager' => [
        'invokables' => [
            'Task' => 'Common\BusinessService\Service\Task',
            'Fee' => 'Common\BusinessService\Service\Fee',
            // Some of these LVA services may be re-usable outside of LVA, if so please move them from the LVA namespace
            'Lva\BusinessDetails' => 'Common\BusinessService\Service\Lva\BusinessDetails',
            'Lva\TradingNames' => 'Common\BusinessService\Service\Lva\TradingNames',
            'Lva\RegisteredAddress' => 'Common\BusinessService\Service\Lva\RegisteredAddress',
            'Lva\ContactDetails' => 'Common\BusinessService\Service\Lva\ContactDetails',
            'Lva\BusinessDetailsChangeTask' => 'Common\BusinessService\Service\Lva\BusinessDetailsChangeTask',
            'Lva\CompanySubsidiaryChangeTask' => 'Common\BusinessService\Service\Lva\CompanySubsidiaryChangeTask',
            'Lva\CompanySubsidiary' => 'Common\BusinessService\Service\Lva\CompanySubsidiary',
            'Lva\DeleteCompanySubsidiary' => 'Common\BusinessService\Service\Lva\DeleteCompanySubsidiary',
            'Lva\LicenceAddresses' => 'Common\BusinessService\Service\Lva\Addresses',
            'Lva\VariationAddresses' => 'Common\BusinessService\Service\Lva\Addresses',
            'Lva\ApplicationAddresses' => 'Common\BusinessService\Service\Lva\Addresses',
            'Lva\DirtyAddresses' => 'Common\BusinessService\Service\Lva\DirtyAddresses',
            'Lva\PhoneContact' => 'Common\BusinessService\Service\Lva\PhoneContact',
            'Lva\AddressesChangeTask' => 'Common\BusinessService\Service\Lva\AddressesChangeTask',
            // Lva
            'Lva\Application' => 'Common\BusinessService\Service\Lva\Application',
            'Lva\ApplicationRevive' => 'Common\BusinessService\Service\Lva\ApplicationRevive',
            'Lva\Licence' => 'Common\BusinessService\Service\Lva\Licence',
            // Goods Vehicles business services
            'Lva\LicenceGoodsVehicles' => 'Common\BusinessService\Service\Lva\GoodsVehicles',
            'Lva\VariationGoodsVehicles' => 'Common\BusinessService\Service\Lva\GoodsVehicles',
            'Lva\ApplicationGoodsVehicles' => 'Common\BusinessService\Service\Lva\ApplicationGoodsVehicles',
            // Psv Vehicles business services
            'Lva\LicencePsvVehicles' => 'Common\BusinessService\Service\Lva\PsvVehicles',
            'Lva\VariationPsvVehicles' => 'Common\BusinessService\Service\Lva\PsvVehicles',
            'Lva\ApplicationPsvVehicles' => 'Common\BusinessService\Service\Lva\ApplicationPsvVehicles',
            // Goods vehicles vehicle business service
            'Lva\LicenceGoodsVehiclesVehicle' => 'Common\BusinessService\Service\Lva\LicenceGoodsVehiclesVehicle',
            'Lva\LicenceGoodsVehiclesRemovedVehicle' =>
                'Common\BusinessService\Service\Lva\LicenceGoodsVehiclesRemovedVehicle',
            'Lva\VariationGoodsVehiclesVehicle' => 'Common\BusinessService\Service\Lva\VariationGoodsVehiclesVehicle',
            'Lva\ApplicationGoodsVehiclesVehicle'
                => 'Common\BusinessService\Service\Lva\ApplicationGoodsVehiclesVehicle',
            'Lva\RequestDisc' => 'Common\BusinessService\Service\Lva\RequestDisc',
            'Lva\ReprintDisc' => 'Common\BusinessService\Service\Lva\ReprintDisc',
            'Lva\CeaseActiveDisc' => 'Common\BusinessService\Service\Lva\CeaseActiveDisc',
            'Lva\DeleteGoodsVehicle' => 'Common\BusinessService\Service\Lva\DeleteGoodsVehicle',
            'Lva\DeleteTransportManagerApplication' =>
                'Common\BusinessService\Service\Lva\DeleteTransportManagerApplication',
            'Lva\TransportManagerApplicationForUser' =>
                'Common\BusinessService\Service\Lva\TransportManagerApplicationForUser',
            'Lva\TransportManagerApplication' =>
                'Common\BusinessService\Service\Lva\TransportManagerApplication',
            'Lva\SendTransportManagerApplication' =>
                'Common\BusinessService\Service\Lva\SendTransportManagerApplication',
            'Lva\TransportManager' =>
                'Common\BusinessService\Service\Lva\TransportManager',
            'Lva\DeltaDeleteTransportManagerLicence' =>
                'Common\BusinessService\Service\Lva\DeltaDeleteTransportManagerLicence',
            'Lva\TransportManagerDetails' =>
                'Common\BusinessService\Service\Lva\TransportManagerDetails',
            'Lva\Person' =>
                'Common\BusinessService\Service\Lva\Person',
            'Lva\OtherLicence' =>
                'Common\BusinessService\Service\Lva\OtherLicence',
            'Lva\PreviousConviction' =>
                'Common\BusinessService\Service\Lva\PreviousConviction',
            'Lva\DeleteOtherLicence' =>
                'Common\BusinessService\Service\Lva\DeleteOtherLicence',
            'Lva\DeletePreviousConviction' =>
                'Common\BusinessService\Service\Lva\DeletePreviousConviction',
            'Lva\TransferVehicles' =>
                'Common\BusinessService\Service\Lva\TransferVehicles',
            'Lva\DeleteOtherEmployment' =>
                'Common\BusinessService\Service\Lva\DeleteOtherEmployment',
            'Lva\Address' =>
                'Common\BusinessService\Service\Lva\Address',
            'TmEmployment' =>
                'Common\BusinessService\Service\TmEmployment',
            // User administration
            'Admin\User' =>
                'Common\BusinessService\Service\Admin\User',
            // Cases business services
            'Cases\Complaint\EnvironmentalComplaint'
                => 'Common\BusinessService\Service\Cases\Complaint\EnvironmentalComplaint',
            'Cases\Complaint\EnvironmentalComplaintTask'
                => 'Common\BusinessService\Service\Cases\Complaint\EnvironmentalComplaintTask',
            'Cases\Submission\Decision' => 'Common\BusinessService\Service\Cases\Submission\Decision',
            'Cases\Submission\Recommendation' => 'Common\BusinessService\Service\Cases\Submission\Recommendation',
            'Cases\Submission\SubmissionActionTask'
                => 'Common\BusinessService\Service\Cases\Submission\SubmissionActionTask',
            // Bus business services
            'Bus\BusReg'
                => 'Common\BusinessService\Service\Bus\BusReg',
        ]
    ],
);
