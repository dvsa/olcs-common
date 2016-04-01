<?php

use Common\Service\Data\Search\SearchType;
use Common\FormService\Form\Lva as LvaFormService;

$release = json_decode(file_get_contents(__DIR__ . '/release.json'), true);

return array(
    'router' => array(
        'routes' => array(
            'application_start' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/application_start_page[/]'
                )
            ),
            'getfile' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/file/:identifier',
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
            ),
            'correspondence_inbox' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/correspondence[/]'
                )
            ),
            'not-found' => array(
                'type' => 'segment',
                'options' =>  array(
                    'route' => '/404[/]',
                    'defaults' => array(
                        'controller' => \Common\Controller\ErrorController::class,
                        'action' => 'notFound'
                    )
                )
            ),
            'server-error' => array(
                'type' => 'segment',
                'options' =>  array(
                    'route' => '/error[/]',
                    'defaults' => array(
                        'controller' => \Common\Controller\ErrorController::class,
                        'action' => 'serverError'
                    )
                )
            ),
            'guides' => array(
                'type' => 'segment',
                'options' =>  array(
                    'route' => '/guides[/]'
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'guide' => array(
                        'type' => 'segment',
                        'options' =>  array(
                            'route' => ':guide[/]',
                            'constraints' => [
                                'guide' => '[a-zA-Z\-0-9]+'
                            ],
                            'defaults' => array(
                                'controller' => \Common\Controller\GuidesController::class,
                                'action' => 'index'
                            )
                        ),
                    ),
                )
            ),
        )
    ),
    'controllers' => array(
        'initializers' => array(
            'Common\Controller\Crud\Initializer'
        ),
        // @NOTE These delegators can live in common as both internal and external app controllers currently use the
        // same adapter
        'delegators' => array(
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
            'Common\Controller\File' => 'Common\Controller\FileController',
            'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
            'TransportManagerReview' => 'Common\Controller\TransportManagerReviewController',
            \Common\Controller\ErrorController::class => \Common\Controller\ErrorController::class,
            \Common\Controller\GuidesController::class => \Common\Controller\GuidesController::class,
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'redirect' => 'Common\Controller\Plugin\Redirect',
        ),
        'factories' => [
            'currentUser' => \Common\Controller\Plugin\CurrentUserFactory::class,
            'ElasticSearch' => 'Common\Controller\Plugin\ElasticSearchFactory',
            'handleQuery' => \Common\Controller\Plugin\HandleQueryFactory::class,
            'handleCommand' => \Common\Controller\Plugin\HandleCommandFactory::class,
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
            'MvcTranslator' => [
                \Common\Util\TranslatorDelegatorFactory::class,
            ]
        ],
        'shared' => array(
            'Helper\FileUpload' => false,
            'CantIncreaseValidator' => false,
            // Create a new request each time
            'CqrsRequest' => false
        ),
        'abstract_factories' => array(
            'Common\Util\AbstractServiceFactory'
        ),
        'aliases' => array(
            'Cache' => 'Zend\Cache\Storage\StorageInterface',
            'DataServiceManager' => 'Common\Service\Data\PluginManager',
            'BundleManager' => 'Common\Service\Data\BundleManager',
            'translator' => 'MvcTranslator',
            'Zend\Log' => 'Logger',
            'TableBuilder' => 'Common\Service\Table\TableBuilderFactory',
            'NavigationFactory' => 'Common\Service\NavigationFactory',
            'QueryService' => \Common\Service\Cqrs\Query\CachingQueryService::class,
        ),
        'invokables' => array(
            'Common\Service\NavigationFactory' => 'Common\Service\NavigationFactory',
            'CrudListener' => 'Common\Controller\Crud\Listener',
            'SectionConfig' => 'Common\Service\Data\SectionConfig',
            'CantIncreaseValidator' => 'Common\Form\Elements\Validators\CantIncreaseValidator',
            'GenericBusinessTypeAdapter'
                => 'Common\Controller\Lva\Adapters\GenericBusinessTypeAdapter',
            'ApplicationConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationConditionsUndertakingsAdapter',
            'VariationConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\VariationConditionsUndertakingsAdapter',
            'LicenceConditionsUndertakingsAdapter'
                => 'Common\Controller\Lva\Adapters\LicenceConditionsUndertakingsAdapter',
            'ApplicationVehicleGoodsAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationVehicleGoodsAdapter',
            'VariationFinancialEvidenceAdapter'
                => 'Common\Controller\Lva\Adapters\VariationFinancialEvidenceAdapter',
            'ApplicationFinancialEvidenceAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationFinancialEvidenceAdapter',
            'ApplicationPeopleAdapter'
                => 'Common\Controller\Lva\Adapters\ApplicationPeopleAdapter',
            'LicencePeopleAdapter'
                => 'Common\Controller\Lva\Adapters\LicencePeopleAdapter',
            'VariationPeopleAdapter'
                => 'Common\Controller\Lva\Adapters\VariationPeopleAdapter',
            'Common\Filesystem\Filesystem' => 'Common\Filesystem\Filesystem',
            'VehicleList' => '\Common\Service\VehicleList\VehicleList',
            'postcode' => 'Common\Service\Postcode\Postcode',
            'email' => 'Common\Service\Email\Email',
            'CompaniesHouseApi' => 'Common\Service\CompaniesHouse\Api',
            'goodsDiscStartNumberValidator' => 'Common\Form\Elements\Validators\GoodsDiscStartNumberValidator',
            'applicationIdValidator' => 'Common\Form\Elements\Validators\ApplicationIdValidator',
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
            'country' => '\Common\Service\Data\Country',
            'Common\Service\Data\Country' => 'Common\Service\Data\Country',
            'Common\Service\Data\Team' => 'Common\Service\Data\Team',
            'Common\Service\Data\Role' => 'Common\Service\Data\Role',
            'Common\Service\Data\RefData' => 'Common\Service\Data\RefData',
            'Common\Service\Data\Licence' => 'Common\Service\Data\Licence',
            'Common\Service\Data\Application' => 'Common\Service\Data\Application',
        ),
        'factories' => array(
            'CommandSender' => \Common\Service\Cqrs\Command\CommandSender::class,
            'QuerySender' => \Common\Service\Cqrs\Query\QuerySender::class,
            'LanguagePreference' => \Common\Preference\Language::class,
            'LanguageListener' => \Common\Preference\LanguageListener::class,
            'CqrsRequest' => \Common\Service\Cqrs\RequestFactory::class,
            \Common\Service\Cqrs\Query\CachingQueryService::class
                => \Common\Service\Cqrs\Query\CachingQueryServiceFactory::class,
            \Common\Service\Cqrs\Query\QueryService::class => \Common\Service\Cqrs\Query\QueryServiceFactory::class,
            'CommandService' => \Common\Service\Cqrs\Command\CommandServiceFactory::class,
            'FormServiceManager' => 'Common\FormService\FormServiceManagerFactory',
            'BusinessServiceManager' => 'Common\BusinessService\BusinessServiceManagerFactory',
            'BusinessRuleManager' => 'Common\BusinessRule\BusinessRuleManagerFactory',
            'ApplicationLvaAdapter' => 'Common\Controller\Lva\Factories\ApplicationLvaAdapterFactory',
            'LicenceLvaAdapter' => 'Common\Controller\Lva\Factories\LicenceLvaAdapterFactory',
            'VariationLvaAdapter' => 'Common\Controller\Lva\Factories\VariationLvaAdapterFactory',
            'Common\Service\Data\LicenceOperatingCentre' => 'Common\Service\Data\LicenceOperatingCentre',
            'Common\Service\Data\ApplicationOperatingCentre' => 'Common\Service\Data\ApplicationOperatingCentre',
            'Common\Service\ShortNotice' => 'Common\Service\ShortNotice',
            'Common\Service\Data\EbsrSubTypeListDataService' => 'Common\Service\Data\EbsrSubTypeListDataService',
            'Common\Service\Data\UserTypesListDataService' => 'Common\Service\Data\UserTypesListDataService',
            'Script' => '\Common\Service\Script\ScriptFactory',
            'Table' => '\Common\Service\Table\TableFactory',
            // Added in a true Zend Framework V2 compatible factory for TableBuilder, eventually to replace Table above.
            'Common\Service\Table\TableBuilderFactory' => 'Common\Service\Table\TableBuilderFactory',
            'ServiceApiResolver' => 'Common\Service\Api\ResolverFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'SectionService' => '\Common\Controller\Service\SectionServiceFactory',
            'category' => '\Common\Service\Data\CategoryDataService',
            'staticList' => 'Common\Service\Data\StaticList',
            'FormAnnotationBuilder' => '\Common\Service\FormAnnotationBuilderFactory',
            'Common\Service\Data\PluginManager' => 'Common\Service\Data\PluginManagerFactory',
            'Common\Service\Data\BundleManager' => 'Common\Service\Data\BundleManagerFactory',
            'Zend\Cache\Storage\StorageInterface' => 'Zend\Cache\Service\StorageCacheFactory',
            'Common\Rbac\Navigation\IsAllowedListener' => 'Common\Rbac\Navigation\IsAllowedListener',
            \Common\Service\Data\Search\SearchTypeManager::class =>
                \Common\Service\Data\Search\SearchTypeManagerFactory::class,
            \Common\Rbac\IdentityProvider::class => \Common\Rbac\IdentityProviderFactory::class
        )
    ),
    /*'search' => [
        'invokables' => [
            'operator'    => LicenceSelfserve::class, // Selfserve licence search
            'licence'     => LicenceSearch::class,
            'application' => \Common\Data\Object\Search\Application::class,
            'case'        => \Common\Data\Object\Search\Cases::class,
            'psv_disc'    => \Common\Data\Object\Search\PsvDisc::class,
            'vehicle'     => \Common\Data\Object\Search\Vehicle::class,
            'vehicle-external' => \Common\Data\Object\Search\VehicleSelfServe::class,
            'address'     => \Common\Data\Object\Search\Address::class,
            'bus'         => \Common\Data\Object\Search\BusRegSelfServe::class,
            'bus_reg'     => \Common\Data\Object\Search\BusReg::class,
            'people'      => \Common\Data\Object\Search\People::class,
            'person'      => PeopleSelfserveSearchIndex::class,
            'user'        => \Common\Data\Object\Search\User::class,
            'publication' => \Common\Data\Object\Search\Publication::class,
            'organisation'     => \Common\Data\Object\Search\Organisation::class,
            'operating-centre' => OperatingCentreSearchIndex::class,
            'traffic-commissioner-publication' => \Common\Data\Object\Search\TrafficCommissionerPublications::class,
        ]
    ],*/
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
            'formElement' => 'Common\Form\View\Helper\FormElement',
            'formElementErrors' => 'Common\Form\View\Helper\FormElementErrors',
            'formErrors' => 'Common\Form\View\Helper\FormErrors',
            'formDateTimeSelect' => 'Common\Form\View\Helper\FormDateTimeSelect',
            'formDateSelect' => \Common\Form\View\Helper\FormDateSelect::class,
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
            'transportManagerApplicationStatus' => 'Common\View\Helper\TransportManagerApplicationStatus',
            'status' => 'Common\View\Helper\Status',
            'address' => 'Common\View\Helper\Address',
            'personName' => 'Common\View\Helper\PersonName',

            // Extended form view helpers, to allow us to use alternative attributes that are not in ZF2's whitelist
            'formbutton'              => \Common\Form\View\Helper\Extended\FormButton::class,
            'formcaptcha'             => \Common\Form\View\Helper\Extended\FormCaptcha::class,
            'formcheckbox'            => \Common\Form\View\Helper\Extended\FormCheckbox::class,
            'formcolor'               => \Common\Form\View\Helper\Extended\FormColor::class,
            'formdate'                => \Common\Form\View\Helper\Extended\FormDate::class,
            'formdatetime'            => \Common\Form\View\Helper\Extended\FormDateTime::class,
            'formdatetimelocal'       => \Common\Form\View\Helper\Extended\FormDateTimeLocal::class,
            'formemail'               => \Common\Form\View\Helper\Extended\FormEmail::class,
            'formfile'                => \Common\Form\View\Helper\Extended\FormFile::class,
            'formhidden'              => \Common\Form\View\Helper\Extended\FormHidden::class,
            'formimage'               => \Common\Form\View\Helper\Extended\FormImage::class,
            'forminput'               => \Common\Form\View\Helper\Extended\FormInput::class,
            'formlabel'               => \Common\Form\View\Helper\Extended\FormLabel::class,
            'formmonth'               => \Common\Form\View\Helper\Extended\FormMonth::class,
            'formmonthselect'         => \Common\Form\View\Helper\Extended\FormMonthSelect::class,
            'formmulticheckbox'       => \Common\Form\View\Helper\Extended\FormMultiCheckbox::class,
            'formnumber'              => \Common\Form\View\Helper\Extended\FormNumber::class,
            'formpassword'            => \Common\Form\View\Helper\Extended\FormPassword::class,
            'formradio'               => \Common\Form\View\Helper\Extended\FormRadio::class,
            'formrange'               => \Common\Form\View\Helper\Extended\FormRange::class,
            'formreset'               => \Common\Form\View\Helper\Extended\FormReset::class,
            'formsearch'              => \Common\Form\View\Helper\Extended\FormSearch::class,
            'formselect'              => \Common\Form\View\Helper\Extended\FormSelect::class,
            'formsubmit'              => \Common\Form\View\Helper\Extended\FormSubmit::class,
            'formtel'                 => \Common\Form\View\Helper\Extended\FormTel::class,
            'formtext'                => \Common\Form\View\Helper\Extended\FormText::class,
            'formtextarea'            => \Common\Form\View\Helper\Extended\FormTextarea::class,
            'formtime'                => \Common\Form\View\Helper\Extended\FormTime::class,
            'formurl'                 => \Common\Form\View\Helper\Extended\FormUrl::class,
            'formweek'                => \Common\Form\View\Helper\Extended\FormWeek::class,
        ),
        'factories' => array(
            'pageId' => \Common\View\Helper\PageId::class,
            'pageTitle' => \Common\View\Helper\PageTitle::class,
            'date' => \Common\View\Helper\Date::class,
            'formRow' => 'Common\Form\View\Helper\FormRow',
            'languageLink' => \Common\View\Helper\LanguageLink::class,
            'currentUser' => \Common\View\Helper\CurrentUserFactory::class,
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
            'YearSelect' => 'Common\Form\Elements\Custom\YearSelect',
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
            'Common\Validator\ValidateIfMultiple' => 'Common\Validator\ValidateIfMultiple',
            'Common\Validator\DateCompare' => 'Common\Validator\DateCompare',
            'Common\Validator\NumberCompare' => 'Common\Validator\NumberCompare',
            'Common\Form\Elements\Validators\DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'Common\Validator\OneOf' => 'Common\Validator\OneOf',
            'Common\Form\Elements\Validators\Date' => 'Common\Form\Elements\Validators\Date',
            'Common\Validator\DateInFuture' => 'Common\Validator\DateInFuture',
            'Common\Validator\DateCompareWithInterval' => 'Common\Validator\DateCompareWithInterval',
        ],
        'aliases' => [
            'ValidateIf' => 'Common\Validator\ValidateIf',
            'ValidateIfMultiple' => 'Common\Validator\ValidateIfMultiple',
            'DateCompare' => 'Common\Validator\DateCompare',
            'NumberCompare' => 'Common\Validator\NumberCompare',
            'DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'OneOf' => 'Common\Validator\OneOf',
            'Date' => 'Common\Form\Elements\Validators\Date',
            'DateInFuture' => 'Common\Validator\DateInFuture',
            'DateCompareWithInterval' => 'Common\Validator\DateCompareWithInterval',
        ]
    ],
    'filters' => [
        'invokables' => [
            'Common\Filter\DateSelectNullifier' => 'Common\Filter\DateSelectNullifier',
            'Common\Filter\DateTimeSelectNullifier' => 'Common\Filter\DateTimeSelectNullifier',
            'Common\Filter\DecompressUploadToTmp' => 'Common\Filter\DecompressUploadToTmp',
            'Common\Filter\DecompressToTmp' => 'Common\Filter\DecompressToTmp'
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
            'Common\Service\Data\Venue' => 'Common\Service\Data\Venue',
            'Common\Service\Data\PiHearing' => 'Common\Service\Data\PiHearing',
            'Common\Service\Data\LicenceListDataService' => 'Common\Service\Data\LicenceListDataService',
            'Common\Service\Data\LicenceOperatingCentre' =>
                'Common\Service\Data\LicenceOperatingCentre',
            'Common\Service\Data\ApplicationOperatingCentre' =>
                'Common\Service\Data\ApplicationOperatingCentre',
            'Common\Service\Data\OcContextListDataService' => 'Common\Service\Data\OcContextListDataService',
            SearchType::class => SearchType::class

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
            'postcode' => 'http://postcode.cit.olcs.mgt.mtpdvsa/',
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
        'identity_provider' => \Common\Rbac\IdentityProvider::class,
        'role_provider' => [\Common\Rbac\Role\RoleProvider::class => []],
        'role_provider_manager' => [
            'factories' => [
                \Common\Rbac\Role\RoleProvider::class => \Common\Rbac\Role\RoleProviderFactory::class
            ]
        ],
        'protection_policy' => \ZfcRbac\Guard\GuardInterface::POLICY_DENY,
    ],
    'cache' => [
        'adapter' => [
            'name' => 'apc',
        ]
    ],
    'form_service_manager' => [
        'invokables' => [
            // OC Forms
            'lva-licence-operating_centres' => LvaFormService\OperatingCentres\LicenceOperatingCentres::class,
            'lva-variation-operating_centres' => LvaFormService\OperatingCentres\VariationOperatingCentres::class,
            'lva-licence-operating_centre' => LvaFormService\OperatingCentre\CommonOperatingCentre::class,
            'lva-variation-operating_centre' => LvaFormService\OperatingCentre\CommonOperatingCentre::class,
            'lva-application-operating_centre' => LvaFormService\OperatingCentre\CommonOperatingCentre::class,

            // Business type forms
            'lva-application-business_type' => LvaFormService\BusinessType\ApplicationBusinessType::class,
            'lva-licence-business_type' => LvaFormService\BusinessType\LicenceBusinessType::class,
            'lva-variation-business_type' => LvaFormService\BusinessType\VariationBusinessType::class,

            // Lva form services
            'lva-licence' => LvaFormService\Licence::class,
            'lva-variation' => LvaFormService\Variation::class,
            'lva-application' => LvaFormService\Application::class,

            // Business details form services
            'lva-licence-business_details' => LvaFormService\BusinessDetails\LicenceBusinessDetails::class,
            'lva-variation-business_details' => LvaFormService\BusinessDetails\VariationBusinessDetails::class,
            'lva-application-business_details' => LvaFormService\BusinessDetails\ApplicationBusinessDetails::class,

            // Addresses form services
            'lva-licence-addresses' => LvaFormService\Addresses::class,
            'lva-variation-addresses' => LvaFormService\Addresses::class,
            'lva-application-addresses' => LvaFormService\Addresses::class,

            // Goods vehicle form services
            'lva-licence-goods-vehicles' => LvaFormService\LicenceGoodsVehicles::class,
            'lva-variation-goods-vehicles' => LvaFormService\VariationGoodsVehicles::class,
            'lva-application-goods-vehicles' => LvaFormService\ApplicationGoodsVehicles::class,

            // Psv vehicles vehicle form services
            'lva-licence-vehicles_psv-vehicle' => LvaFormService\LicencePsvVehiclesVehicle::class,
            'lva-variation-vehicles_psv-vehicle' => LvaFormService\ApplicationPsvVehiclesVehicle::class,
            'lva-application-vehicles_psv-vehicle' => LvaFormService\ApplicationPsvVehiclesVehicle::class,

            // Goods vehicle filter form services
            'lva-licence-goods-vehicles-filters' => LvaFormService\LicenceGoodsVehiclesFilters::class,
            'lva-variation-goods-vehicles-filters' => LvaFormService\CommonGoodsVehiclesFilters::class,
            'lva-application-goods-vehicles-filters' => LvaFormService\CommonGoodsVehiclesFilters::class,

            // PSV filter form services
            'lva-psv-vehicles-filters' => LvaFormService\CommonPsvVehiclesFilters::class,

            // Vehicle search form services
            'lva-vehicles-search' => LvaFormService\CommonVehiclesSearch::class,

            // Common vehicle services
            'lva-licence-variation-vehicles' => LvaFormService\LicenceVariationVehicles::class,
            'lva-generic-vehicles-vehicle' => LvaFormService\GenericVehiclesVehicle::class,

            // Type of licence
            'lva-licence-type-of-licence' => LvaFormService\TypeOfLicence\LicenceTypeOfLicence::class,
            'lva-application-type-of-licence' => LvaFormService\TypeOfLicence\ApplicationTypeOfLicence::class,
            'lva-variation-type-of-licence' => LvaFormService\TypeOfLicence\VariationTypeOfLicence::class,

            // People form services
            'lva-licence-people' => LvaFormService\People\LicencePeople::class,
            'lva-variation-people' => LvaFormService\People\VariationPeople::class,
            'lva-application-people' => LvaFormService\People\ApplicationPeople::class,
            'lva-licence-sole_trader' => LvaFormService\People\SoleTrader\LicenceSoleTrader::class,
            'lva-variation-sole_trader' => LvaFormService\People\SoleTrader\VariationSoleTrader::class,
            'lva-application-sole_trader' => LvaFormService\People\SoleTrader\ApplicationSoleTrader::class,

            // Community Licences form services
            'lva-licence-community_licences' => LvaFormService\CommunityLicences\LicenceCommunityLicences::class,
            'lva-variation-community_licences' => LvaFormService\CommunityLicences\VariationCommunityLicences::class,
            'lva-application-community_licences'
                => LvaFormService\CommunityLicences\ApplicationCommunityLicences::class,

            // Safety form services
            'lva-licence-safety' => LvaFormService\Safety::class,
            'lva-variation-safety' => LvaFormService\Safety::class,
            'lva-application-safety' => LvaFormService\Safety::class,

            // Conditions and Undertakings form services
            'lva-licence-conditions_undertakings'
                => LvaFormService\ConditionsUndertakings\LicenceConditionsUndertakings::class,
            'lva-variation-conditions_undertakings'
                => LvaFormService\ConditionsUndertakings\VariationConditionsUndertakings::class,
            'lva-application-conditions_undertakings'
                => LvaFormService\ConditionsUndertakings\ApplicationConditionsUndertakings::class,

            // Financial History form services
            'lva-licence-financial_history' => LvaFormService\FinancialHistory::class,
            'lva-variation-financial_history' => LvaFormService\FinancialHistory::class,
            'lva-application-financial_history' => LvaFormService\FinancialHistory::class,

            // Financial Evidence form services
            'lva-variation-financial_evidence' => LvaFormService\VariationFinancialEvidence::class,
            'lva-application-financial_evidence' => LvaFormService\FinancialEvidence::class,

            // Declarations (undertakings) form services
            'lva-variation-undertakings' => LvaFormService\Undertakings::class,
            'lva-application-undertakings' => LvaFormService\Undertakings::class,

            // Taxi/PHV form services
            'lva-licence-taxi_phv' => LvaFormService\LicenceTaxiPhv::class,
            'lva-variation-taxi_phv' => LvaFormService\TaxiPhv::class,
            'lva-application-taxi_phv' => LvaFormService\TaxiPhv::class,

            // Licence History form services
            'lva-application-licence_history' => LvaFormService\LicenceHistory::class,

            // Convictions & Penalties form services
            'lva-variation-convictions_penalties' => LvaFormService\ConvictionsPenalties::class,
            'lva-application-convictions_penalties' => LvaFormService\ConvictionsPenalties::class,

            // Vehicles Declaratinos form services
            'lva-variation-vehicles_declarations' => LvaFormService\VehiclesDeclarations::class,
            'lva-application-vehicles_declarations' => LvaFormService\VehiclesDeclarations::class,

            // PSV Vehicles form services
            'lva-licence-vehicles_psv' => LvaFormService\PsvVehicles::class,
            'lva-variation-vehicles_psv' => LvaFormService\VariationPsvVehicles::class,
            'lva-application-vehicles_psv' => LvaFormService\PsvVehicles::class,

            // Discs form services
            'lva-licence-discs' => LvaFormService\PsvDiscs::class,
            'lva-variation-discs' => LvaFormService\PsvDiscs::class,

            'lva-licence-transport_managers' => LvaFormService\TransportManager\LicenceTransportManager::class,
            'lva-variation-transport_managers' => LvaFormService\TransportManager\VariationTransportManager::class,
            'lva-application-transport_managers' => LvaFormService\TransportManager\ApplicationTransportManager::class,
        ]
    ],
    'business_rule_manager' => [
        'invokables' => [
            'Task' => 'Common\BusinessRule\Rule\Task',
            'Fee' => 'Common\BusinessRule\Rule\Fee',
            'TradingNames' => 'Common\BusinessRule\Rule\TradingNames',
            'BusinessDetails' => 'Common\BusinessRule\Rule\BusinessDetails',
            'CheckDate' => 'Common\BusinessRule\Rule\CheckDate',
        ]
    ],
    'business_service_manager' => [
        'invokables' => [
            'Task' => 'Common\BusinessService\Service\Task',
            'Fee' => 'Common\BusinessService\Service\Fee',
            // Some of these LVA services may be re-usable outside of LVA, if so please move them from the LVA namespace
            'Lva\TradingNames' => 'Common\BusinessService\Service\Lva\TradingNames',
            'Lva\ContactDetails' => 'Common\BusinessService\Service\Lva\ContactDetails',
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
            // Psv Vehicles business services
            'Lva\LicencePsvVehicles' => 'Common\BusinessService\Service\Lva\PsvVehicles',
            'Lva\VariationPsvVehicles' => 'Common\BusinessService\Service\Lva\PsvVehicles',
            'Lva\ApplicationPsvVehicles' => 'Common\BusinessService\Service\Lva\ApplicationPsvVehicles',
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
            // Cases business services
            'Cases\Submission\SubmissionAssignmentTask'
            => 'Common\BusinessService\Service\Cases\Submission\SubmissionAssignmentTask',
            'Cases\Submission\Submission' => 'Common\BusinessService\Service\Cases\Submission\Submission',
            // Bus business services
            'Bus\BusReg'
                => 'Common\BusinessService\Service\Bus\BusReg',
            'CreateSeparatorSheet' => 'Common\BusinessService\Service\CreateSeparatorSheet',
            'Lva\AccessCorrespondence' => 'Common\BusinessService\Service\Lva\AccessCorrespondence',
            // Operator services
            'Lva\TransportConsultant' => 'Common\BusinessService\Service\Lva\TransportConsultant',
            'Lva\ContinueLicence'
                => 'Common\BusinessService\Service\Lva\ContinueLicence',
        ]
    ],
    'email' => [
        'default' => [
            'from_address' => 'donotreply@otc.gsi.gov.uk',
            'from_name'  => 'OLCS do not reply'
        ]
    ],
    'translator' => [
        'replacements' => include_once(__DIR__ . '/language/replacements.php')
    ],
    'cacheable_queries' => [
        \Dvsa\Olcs\Transfer\Query\User\RoleList::class => ['persistent' => true]
    ]
);
