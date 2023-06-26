<?php

use Common\Auth\Adapter\CommandAdapter;
use Common\Auth\Adapter\CommandAdapterFactory;
use Common\Auth\Service\AuthenticationServiceFactory;
use Common\Auth\Service\AuthenticationServiceInterface;
use Common\Form\Element\DynamicMultiCheckbox;
use Common\Form\Element\DynamicMultiCheckboxFactory;
use Common\Form\Element\DynamicRadio;
use Common\Form\Element\DynamicRadioFactory;
use Common\Form\Element\DynamicRadioHtml;
use Common\Form\Element\DynamicRadioHtmlFactory;
use Common\Form\Element\DynamicSelect;
use Common\Form\Element\DynamicSelectFactory;
use Common\Form\Elements\Custom\OlcsCheckbox;
use Common\Form\View\Helper\FormInputSearch;
use Common\Service\Cqrs\Command\CommandSender;
use Common\Service\Data\Search\SearchType;
use Common\FormService\Form\Lva as LvaFormService;
use Common\FormService\Form\Continuation as ContinuationFormService;
use Common\Form\View\Helper\Readonly as ReadonlyFormHelper;
use Common\Service\Data as DataService;
use Common\Service\Helper as HelperService;
use Common\Service\Qa as QaService;
use Common\Service\Translator\TranslationLoader;
use Common\Service\Translator\TranslationLoaderFactory;
use Common\Data\Mapper\Permits as PermitsMapper;
use Common\Data\Mapper\Licence\Surrender as SurrenderMapper;
use Common\View\Helper\Panel;
use ZfcRbac\Identity\IdentityProviderInterface;

$release = json_decode(file_get_contents(__DIR__ . '/release.json'), true);

return [
    'router' => [
        'routes' => array_merge(
            require(__DIR__ . '/routes/general.php'),
            require(__DIR__ . '/routes/continuations.php')
        )
    ],
    'controllers' => [
        // @NOTE These delegators can live in common as both internal and external app controllers currently use the
        // same adapter. Self Serve registers these itself within the application module.
        'delegators' => [
            'LvaApplication/BusinessType' => [
                // @NOTE: we need an associative array when we need to override the
                // delegator elsewhere, such as in selfserve or internal
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ],
            'LvaLicence/BusinessType' => [
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ],
            'LvaVariation/BusinessType' => [
                'delegator' => 'Common\Controller\Lva\Delegators\GenericBusinessTypeDelegator'
            ],
            'LvaApplication/FinancialEvidence' => [
                Common\Controller\Lva\Delegators\ApplicationFinancialEvidenceDelegator::class,
            ],
            'LvaVariation/FinancialEvidence' => [
                Common\Controller\Lva\Delegators\VariationFinancialEvidenceDelegator::class,
            ],
            'LvaLicence/People' => [
                'Common\Controller\Lva\Delegators\LicencePeopleDelegator'
            ],
            'LvaVariation/People' => [
                'Common\Controller\Lva\Delegators\VariationPeopleDelegator'
            ],
            'LvaApplication/People' => [
                'Common\Controller\Lva\Delegators\ApplicationPeopleDelegator'
            ],
            'LvaLicence/TransportManagers' => [
                Common\Controller\Lva\Delegators\LicenceTransportManagerDelegator::class,
            ],
            'LvaVariation/TransportManagers' => [
                Common\Controller\Lva\Delegators\VariationTransportManagerDelegator::class,
            ],
            'LvaApplication/TransportManagers' => [
                Common\Controller\Lva\Delegators\ApplicationTransportManagerDelegator::class,
            ],
            'LvaDirectorChange/People' => [
                'Common\Controller\Lva\Delegators\VariationPeopleDelegator'
            ],
        ],
        'abstract_factories' => [
            'Common\Controller\Lva\AbstractControllerFactory',
        ],
        'invokables' => [
            'Common\Controller\File' => 'Common\Controller\FileController',
            'Common\Controller\FormRewrite' => 'Common\Controller\FormRewriteController',
            Common\Controller\TransportManagerReviewController::class =>
                Common\Controller\TransportManagerReviewController::class,
            \Common\Controller\ErrorController::class => \Common\Controller\ErrorController::class,
            \Common\Controller\GuidesController::class => \Common\Controller\GuidesController::class,
            'ContinuationController/Start' => \Common\Controller\Continuation\StartController::class,
            'ContinuationController/Checklist' => \Common\Controller\Continuation\ChecklistController::class,
            'ContinuationController/ConditionsUndertakings' =>
                \Common\Controller\Continuation\ConditionsUndertakingsController::class,
            'ContinuationController/Finances' => \Common\Controller\Continuation\FinancesController::class,
            'ContinuationController/OtherFinances' => \Common\Controller\Continuation\OtherFinancesController::class,
            'ContinuationController/InsufficientFinances' =>
                \Common\Controller\Continuation\InsufficientFinancesController::class,
            'ContinuationController/Declaration' => \Common\Controller\Continuation\DeclarationController::class,
            'ContinuationController/Payment' => \Common\Controller\Continuation\PaymentController::class,
            'ContinuationController/Success' => \Common\Controller\Continuation\SuccessController::class,
            'ContinuationController/Review' => \Common\Controller\Continuation\ReviewController::class,
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'redirect' => 'Common\Controller\Plugin\Redirect',
            \Common\Controller\Plugin\Redirect::class => \Common\Controller\Plugin\Redirect::class,
        ],
        'factories' => [
            'currentUser' => \Common\Controller\Plugin\CurrentUserFactory::class,
            \Common\Controller\Plugin\CurrentUser::class => \Common\Controller\Plugin\CurrentUserFactory::class,
            'ElasticSearch' => \Common\Controller\Plugin\ElasticSearchFactory::class,
            'handleQuery' => \Common\Controller\Plugin\HandleQueryFactory::class,
            \Common\Controller\Plugin\HandleQuery::class => \Common\Controller\Plugin\HandleQueryFactory::class,
            'handleCommand' => \Common\Controller\Plugin\HandleCommandFactory::class,
            \Common\Controller\Plugin\HandleCommand::class => \Common\Controller\Plugin\HandleCommandFactory::class,
            'featuresEnabled' => \Common\Controller\Plugin\FeaturesEnabledFactory::class,
            'featuresEnabledForMethod' => \Common\Controller\Plugin\FeaturesEnabledForMethodFactory::class,
        ]
    ],
    'console' => [
        'router' => [
            'routes' => [
                'route101' => [
                    'options' => [
                        'route' => 'formrewrite [olcs|common|selfserve]:formnamespace',
                        'defaults' => [
                            'controller' => 'Common\Controller\FormRewrite',
                            'action' => 'index'
                        ]
                    ]
                ],
                'route102' => [
                    'options' => [
                        'route' => 'formcleanup [olcs|common|selfserve]:formnamespace',
                        'defaults' => [
                            'controller' => 'Common\Controller\FormRewrite',
                            'action' => 'cleanup'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'version' => (isset($release['version']) ? $release['version'] : ''),
    'service_manager' => [
        'shared' => [
            'Helper\FileUpload' => false,
            'CantIncreaseValidator' => false,
            // Create a new request each time
            'CqrsRequest' => false
        ],
        'abstract_factories' => [
            'Common\Util\AbstractServiceFactory'
        ],
        'aliases' => [
            'Cache' => 'Laminas\Cache\Storage\StorageInterface',
            'DataServiceManager' => 'Common\Service\Data\PluginManager',
            'translator' => 'MvcTranslator',
            'Laminas\Log' => 'Logger',
            'TableBuilder' => 'Common\Service\Table\TableBuilderFactory',
            'NavigationFactory' => 'Common\Service\NavigationFactory',
            'QueryService' => \Common\Service\Cqrs\Query\CachingQueryService::class,
            'CommandSender' => CommandSender::class,
            'Review\ConditionsUndertakings' => Common\Service\Review\ConditionsUndertakingsReviewService::class,
            'Data\Address' => DataService\AddressDataService::class,

            'Helper\FlashMessenger' => HelperService\FlashMessengerHelperService::class,
            'Helper\Form' => HelperService\FormHelperService::class,
            'Helper\Guidance' => HelperService\GuidanceHelperService::class,
            'Helper\Translation' => HelperService\TranslationHelperService::class,
            'Helper\TransportManager' => HelperService\TransportManagerHelperService::class,
            'Helper\Url' => HelperService\UrlHelperService::class,
            'Lva\People' => Common\Service\Lva\PeopleLvaService::class,
            'Lva\Variation' => Common\Service\Lva\VariationLvaService::class,
        ],
        'invokables' => [
            'Common\Service\NavigationFactory' => 'Common\Service\NavigationFactory',
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
            'CompaniesHouseApi' => 'Common\Service\CompaniesHouse\Api',
            'applicationIdValidator' => 'Common\Form\Elements\Validators\ApplicationIdValidator',
            'totalVehicleAuthorityValidator' => 'Common\Form\Elements\Validators\Lva\TotalVehicleAuthorityValidator',
            'section.vehicle-safety.vehicle.formatter.vrm' =>
                'Common\Service\Section\VehicleSafety\Vehicle\Formatter\Vrm',
            'Common\Rbac\UserProvider' => 'Common\Rbac\UserProvider',
            'DataMapper\DashboardTmApplications' => 'Common\Service\Table\DataMapper\DashboardTmApplications',
            'QaCheckboxFactory' => QaService\CheckboxFactory::class,
            'QaTextFactory' => QaService\TextFactory::class,
            'QaRadioFactory' => QaService\RadioFactory::class,
            'QaFieldsetFactory' => QaService\FieldsetFactory::class,
            'QaValidatorsAdder' => QaService\ValidatorsAdder::class,
            'QaCommonHtmlAdder' => QaService\Custom\Common\HtmlAdder::class,
            'QaEcmtYesNoRadioFactory' => QaService\Custom\Ecmt\YesNoRadioFactory::class,
            'QaEcmtRestrictedCountriesMultiCheckboxFactory'
                => QaService\Custom\Ecmt\RestrictedCountriesMultiCheckboxFactory::class,
            'QaEcmtInternationalJourneysIsValidHandler' =>
                QaService\Custom\Ecmt\InternationalJourneysIsValidHandler::class,
            'QaEcmtAnnualTripsAbroadIsValidHandler' =>
                QaService\Custom\Ecmt\AnnualTripsAbroadIsValidHandler::class,
            'QaBilateralYesNoValueOptionsGenerator' =>
                QaService\Custom\Bilateral\YesNoValueOptionsGenerator::class,
            'QaBilateralCabotageOnlyYesNoRadioFactory' =>
                QaService\Custom\Bilateral\CabotageOnlyYesNoRadioFactory::class,
            'QaBilateralStandardAndCabotageYesNoRadioFactory' =>
                QaService\Custom\Bilateral\StandardAndCabotageYesNoRadioFactory::class,
            'QaBilateralRadioFactory' =>
                QaService\Custom\Bilateral\RadioFactory::class,
            'QaBilateralYesNoRadioOptionsApplier' => QaService\Custom\Bilateral\YesNoRadioOptionsApplier::class,

            'QaBilateralNoOfPermitsFieldsetPopulator' =>
                QaService\Custom\Bilateral\NoOfPermitsFieldsetPopulator::class,
            'QaBilateralNoOfPermitsMoroccoFieldsetPopulator' =>
                QaService\Custom\Bilateral\NoOfPermitsMoroccoFieldsetPopulator::class,
            'QaBilateralPermitUsageIsValidHandler' => QaService\Custom\Bilateral\PermitUsageIsValidHandler::class,
            'QaBilateralStandardAndCabotageSubmittedAnswerGenerator' =>
                QaService\Custom\Bilateral\StandardAndCabotageSubmittedAnswerGenerator::class,
            'QaDateTimeFactory' => QaService\DateTimeFactory::class,

            'QaRoadworthinessMakeAndModelFieldsetModifier' =>
                QaService\FieldsetModifier\RoadworthinessMakeAndModelFieldsetModifier::class,

            'QaEcmtNoOfPermitsSingleDataTransformer' =>
                QaService\DataTransformer\EcmtNoOfPermitsSingleDataTransformer::class,

            Common\Data\Mapper\DefaultMapper::class => Common\Data\Mapper\DefaultMapper::class,
            SurrenderMapper\OperatorLicence::class => SurrenderMapper\OperatorLicence::class,
            SurrenderMapper\CommunityLicence::class => SurrenderMapper\CommunityLicence::class,
            \Common\Service\Helper\ResponseHelperService::class => \Common\Service\Helper\ResponseHelperService::class,
            \Common\Form\FormValidator::class => \Common\Form\FormValidator::class,

            'Zend\Authentication\AuthenticationService' => \Laminas\Authentication\AuthenticationService::class,
            DataService\UserTypesListDataService::class => DataService\UserTypesListDataService::class,
        ],
        'factories' => [
            DataService\AbstractDataServiceServices::class => DataService\AbstractDataServiceServicesFactory::class,
            DataService\AbstractListDataServiceServices::class => DataService\AbstractListDataServiceServicesFactory::class,
            DataService\AddressDataService::class => DataService\AbstractDataServiceFactory::class,
            DataService\Application::class => DataService\AbstractDataServiceFactory::class,
            DataService\ApplicationPathGroup::class => DataService\AbstractDataServiceFactory::class,
            DataService\BusRegBrowseListDataService::class => DataService\AbstractDataServiceFactory::class,
            DataService\BusRegSearchViewListDataService::class => DataService\AbstractDataServiceFactory::class,
            'category' => DataService\CategoryDataService::class,
            DataService\ContactDetails::class => DataService\AbstractListDataServiceFactory::class,
            DataService\Country::class => DataService\AbstractDataServiceFactory::class,
            'country' => DataService\AbstractDataServiceFactory::class,
            DataService\FeeType::class => DataService\AbstractDataServiceFactory::class,
            DataService\FeeTypeDataService::class => DataService\AbstractDataServiceFactory::class,
            DataService\IrhpPermitType::class => DataService\AbstractDataServiceFactory::class,
            DataService\Licence::class => DataService\AbstractDataServiceFactory::class,
            DataService\LocalAuthority::class => DataService\AbstractDataServiceFactory::class,
            DataService\RefData::class => DataService\RefDataFactory::class,
            DataService\RefDataServices::class => DataService\RefDataServicesFactory::class,
            DataService\Role::class => DataService\AbstractDataServiceFactory::class,
            DataService\SiCategoryType::class => DataService\AbstractDataServiceFactory::class,
            'staticList' => DataService\StaticListFactory::class,
            DataService\Surrender::class => DataService\AbstractDataServiceFactory::class,
            DataService\TrafficArea::class => DataService\AbstractDataServiceFactory::class,

            HelperService\FileUploadHelperService::class => HelperService\FileUploadHelperService::class,
            HelperService\FlashMessengerHelperService::class => HelperService\FlashMessengerHelperServiceFactory::class,
            HelperService\FormHelperService::class => HelperService\FormHelperServiceFactory::class,
            HelperService\GuidanceHelperService::class => HelperService\GuidanceHelperServiceFactory::class,
            HelperService\TranslationHelperService::class => HelperService\TranslationHelperServiceFactory::class,
            HelperService\TransportManagerHelperService::class => HelperService\TransportManagerHelperServiceFactory::class,
            HelperService\UrlHelperService::class => HelperService\UrlHelperServiceFactory::class,

            Common\Service\Lva\PeopleLvaService::class => Common\Service\Lva\PeopleLvaServiceFactory::class,
            Common\Service\Lva\VariationLvaService::class => Common\Service\Lva\VariationLvaServiceFactory::class,

            CommandSender::class => CommandSender::class,
            'QuerySender' => \Common\Service\Cqrs\Query\QuerySender::class,
            'LanguagePreference' => \Common\Preference\Language::class,
            'LanguageListener' => \Common\Preference\LanguageListener::class,
            'CqrsRequest' => \Common\Service\Cqrs\RequestFactory::class,
            \Common\Service\Cqrs\Query\CachingQueryService::class
                => \Common\Service\Cqrs\Query\CachingQueryServiceFactory::class,
            \Common\Service\Cqrs\Query\QueryService::class => \Common\Service\Cqrs\Query\QueryServiceFactory::class,
            'CommandService' => \Common\Service\Cqrs\Command\CommandServiceFactory::class,
            'FormServiceManager' => 'Common\FormService\FormServiceManagerFactory',
            'ApplicationLvaAdapter' => 'Common\Controller\Lva\Factories\ApplicationLvaAdapterFactory',
            'LicenceLvaAdapter' => 'Common\Controller\Lva\Factories\LicenceLvaAdapterFactory',
            'VariationLvaAdapter' => 'Common\Controller\Lva\Factories\VariationLvaAdapterFactory',
            'LicenceTransportManagerAdapter' =>
                Common\Controller\Lva\Factories\Adapter\LicenceTransportManagerAdapterFactory::class,
            'ApplicationTransportManagerAdapter' =>
                Common\Controller\Lva\Factories\Adapter\ApplicationTransportManagerAdapterFactory::class,
            'VariationTransportManagerAdapter' =>
                Common\Controller\Lva\Factories\Adapter\VariationTransportManagerAdapterFactory::class,
            'Script' => '\Common\Service\Script\ScriptFactory',
            'Table' => '\Common\Service\Table\TableFactory',
            \Common\Service\Table\TableFactory::class => \Common\Service\Table\TableFactory::class,
            // Added in a true Laminas Framework V2 compatible factory for TableBuilder, eventually to replace Table above.
            'Common\Service\Table\TableBuilderFactory' => 'Common\Service\Table\TableBuilderFactory',
            'ServiceApiResolver' => 'Common\Service\Api\ResolverFactory',
            'navigation' => 'Laminas\Navigation\Service\DefaultNavigationFactory',
            'SectionService' => '\Common\Controller\Service\SectionServiceFactory',
            'FormAnnotationBuilder' => '\Common\Service\FormAnnotationBuilderFactory',
            'Common\Service\Data\PluginManager' => Common\Service\Data\PluginManagerFactory::class,
            'Laminas\Cache\Storage\StorageInterface' => 'Laminas\Cache\Service\StorageCacheFactory',
            \Common\Rbac\Navigation\IsAllowedListener::class => Common\Rbac\Navigation\IsAllowedListener::class,
            \Common\Service\Data\Search\SearchTypeManager::class =>
                \Common\Service\Data\Search\SearchTypeManagerFactory::class,
            \Common\Rbac\PidIdentityProvider::class => \Common\Rbac\PidIdentityProviderFactory::class,
            \Common\Rbac\JWTIdentityProvider::class => \Common\Rbac\JWTIdentityProviderFactory::class,
            \Common\Service\AntiVirus\Scan::class => \Common\Service\AntiVirus\Scan::class,
            'QaCommonWarningAdder' => QaService\Custom\Common\WarningAdderFactory::class,
            'QaCommonIsValidBasedWarningAdder' => QaService\Custom\Common\IsValidBasedWarningAdderFactory::class,
            'QaCommonFileUploadFieldsetGenerator' => QaService\Custom\Common\FileUploadFieldsetGeneratorFactory::class,
            'QaCheckboxFieldsetPopulator' => QaService\CheckboxFieldsetPopulatorFactory::class,
            'QaTextFieldsetPopulator' => QaService\TextFieldsetPopulatorFactory::class,
            'QaRadioFieldsetPopulator' => QaService\RadioFieldsetPopulatorFactory::class,
            'QaFieldsetAdder' => QaService\FieldsetAdderFactory::class,
            'QaFieldsetPopulator' => QaService\FieldsetPopulatorFactory::class,
            'QaFieldsetPopulatorProvider' => QaService\FieldsetPopulatorProviderFactory::class,
            'QaTranslateableTextHandler' => QaService\TranslateableTextHandlerFactory::class,
            'QaTranslateableTextParameterHandler' => QaService\TranslateableTextParameterHandlerFactory::class,
            'QaFormattedTranslateableTextParametersGenerator' =>
                QaService\FormattedTranslateableTextParametersGeneratorFactory::class,
            'QaEcmtNoOfPermitsEitherStrategySelectingFieldsetPopulator' =>
                QaService\Custom\Ecmt\NoOfPermitsEitherStrategySelectingFieldsetPopulatorFactory::class,
            'QaEcmtNoOfPermitsBothStrategySelectingFieldsetPopulator' =>
                QaService\Custom\Ecmt\NoOfPermitsBothStrategySelectingFieldsetPopulatorFactory::class,
            'QaEcmtNoOfPermitsSingleFieldsetPopulator' =>
                QaService\Custom\Ecmt\NoOfPermitsSingleFieldsetPopulatorFactory::class,
            'QaEcmtNoOfPermitsEitherFieldsetPopulator' =>
                QaService\Custom\Ecmt\NoOfPermitsEitherFieldsetPopulatorFactory::class,
            'QaEcmtNoOfPermitsBothFieldsetPopulator' =>
                QaService\Custom\Ecmt\NoOfPermitsBothFieldsetPopulatorFactory::class,
            'QaEcmtNoOfPermitsBaseInsetTextGenerator' =>
                QaService\Custom\Ecmt\NoOfPermitsBaseInsetTextGeneratorFactory::class,
            'QaEcmtPermitUsageFieldsetPopulator' =>
                QaService\Custom\Ecmt\PermitUsageFieldsetPopulatorFactory::class,
            'QaEcmtCheckEcmtNeededFieldsetPopulator' =>
                QaService\Custom\Ecmt\CheckEcmtNeededFieldsetPopulatorFactory::class,
            'QaEcmtRestrictedCountriesFieldsetPopulator' =>
                QaService\Custom\Ecmt\RestrictedCountriesFieldsetPopulatorFactory::class,
            'QaEcmtAnnualTripsAbroadFieldsetPopulator' =>
                QaService\Custom\Ecmt\AnnualTripsAbroadFieldsetPopulatorFactory::class,
            'QaEcmtInternationalJourneysFieldsetPopulator' =>
                QaService\Custom\Ecmt\InternationalJourneysFieldsetPopulatorFactory::class,
            'QaEcmtShortTermEarliestPermitDateFieldsetPopulator' =>
                QaService\Custom\EcmtShortTerm\EarliestPermitDateFieldsetPopulatorFactory::class,
            'QaEcmtRemovalPermitStartDateFieldsetPopulator' =>
                QaService\Custom\EcmtRemoval\PermitStartDateFieldsetPopulatorFactory::class,
            'QaEcmtNiWarningConditionalAdder' =>
                QaService\Custom\Ecmt\NiWarningConditionalAdderFactory::class,
            'QaEcmtInternationalJourneysDataHandler' =>
                QaService\Custom\Ecmt\InternationalJourneysDataHandlerFactory::class,
            'QaEcmtAnnualTripsAbroadDataHandler' =>
                QaService\Custom\Ecmt\AnnualTripsAbroadDataHandlerFactory::class,
            'QaEcmtSectorsFieldsetPopulator' =>
                QaService\Custom\Ecmt\SectorsFieldsetPopulatorFactory::class,
            'QaEcmtInfoIconAdder' =>
                QaService\Custom\Ecmt\InfoIconAdderFactory::class,
            'QaCertRoadworthinessMotExpiryDateFieldsetPopulator' =>
                QaService\Custom\CertRoadworthiness\MotExpiryDateFieldsetPopulatorFactory::class,
            'QaBilateralStandardYesNoValueOptionsGenerator' =>
                QaService\Custom\Bilateral\StandardYesNoValueOptionsGeneratorFactory::class,
            'QaBilateralYesNoWithMarkupForNoPopulator' =>
                QaService\Custom\Bilateral\YesNoWithMarkupForNoPopulatorFactory::class,
            'QaBilateralPermitUsageFieldsetPopulator' =>
                QaService\Custom\Bilateral\PermitUsageFieldsetPopulatorFactory::class,
            'QaBilateralCabotageOnlyFieldsetPopulator' =>
                QaService\Custom\Bilateral\CabotageOnlyFieldsetPopulatorFactory::class,
            'QaBilateralStandardAndCabotageFieldsetPopulator' =>
                QaService\Custom\Bilateral\StandardAndCabotageFieldsetPopulatorFactory::class,
            'QaBilateralThirdCountryFieldsetPopulator' =>
                QaService\Custom\Bilateral\ThirdCountryFieldsetPopulatorFactory::class,
            'QaBilateralEmissionsStandardsFieldsetPopulator' =>
                QaService\Custom\Bilateral\EmissionsStandardsFieldsetPopulatorFactory::class,
            'QaBilateralPermitUsageDataHandler' => QaService\Custom\Bilateral\PermitUsageDataHandlerFactory::class,
            'QaBilateralStandardAndCabotageDataHandler' =>
                QaService\Custom\Bilateral\StandardAndCabotageDataHandlerFactory::class,
            'QaBilateralStandardAndCabotageIsValidHandler' =>
                QaService\Custom\Bilateral\StandardAndCabotageIsValidHandlerFactory::class,

            'QaFieldsetModifier' => QaService\FieldsetModifier\FieldsetModifierFactory::class,

            'QaApplicationStepsPostDataTransformer' =>
                QaService\DataTransformer\ApplicationStepsPostDataTransformerFactory::class,
            'QaDataTransformerProvider' => QaService\DataTransformer\DataTransformerProviderFactory::class,

            Common\Service\Review\AbstractReviewServiceServices::class
                => Common\Service\Review\AbstractReviewServiceServicesFactory::class,
            Common\Service\Review\ConditionsUndertakingsReviewService::class
                => Common\Service\Review\GenericFactory::class,
            'Review\LicenceConditionsUndertakings'
                => Common\Service\Review\LicenceConditionsUndertakingsReviewServiceFactory::class,

            PermitsMapper\NoOfPermits::class => PermitsMapper\NoOfPermitsFactory::class,
            Common\Service\User\LastLoginService::class => Common\Service\User\LastLoginServiceFactory::class,
            'HtmlPurifier' => \Common\Service\Utility\HtmlPurifierFactory::class,
           \Common\Form\View\Helper\Extended\FormLabel::class => \Common\Form\View\Helper\Extended\FormLabelFactory::class,
            \Common\Form\Elements\Validators\Messages\FormElementMessageFormatter::class => \Common\Form\Elements\Validators\Messages\FormElementMessageFormatterFactory::class,

            AuthenticationServiceInterface::class => AuthenticationServiceFactory::class,
            CommandAdapter::class => CommandAdapterFactory::class,
            \Laminas\Authentication\Storage\Session::class => \Common\Auth\SessionFactory::class,
            IdentityProviderInterface::class => \Common\Rbac\IdentityProviderFactory::class,
            \Common\Auth\Service\RefreshTokenService::class => \Common\Auth\Service\RefreshTokenServiceFactory::class,
            \Common\Data\Mapper\Lva\GoodsVehiclesVehicle::class => \Common\Data\Mapper\Lva\GoodsVehiclesVehicleFactory::class,
        ]
    ],
    'file_uploader' => [
        'default' => 'ContentStore',
        'config' => [
            'location' => 'documents',
            'defaultPath' => '[locale]/[doc_type_name]/[year]/[month]', // e.g. gb/publications/2015/03
        ]
    ],
    'navigation_helpers' =>  [
        'invokables' => [
            'menuRbac' => Common\View\Helper\Navigation\MenuRbac::class,
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'formRadioOption' => \Common\Form\View\Helper\FormRadioOption::class,
            'formRadioHorizontal' => \Common\Form\View\Helper\FormRadioHorizontal::class,
            'formCheckboxAdvanced' => \Common\Form\View\Helper\FormCheckboxAdvanced::class,
            'formRadioVertical' => \Common\Form\View\Helper\FormRadioVertical::class,
            'form' => 'Common\Form\View\Helper\Form',
            'formCollection' => Common\Form\View\Helper\FormCollection::class,
            'formDateTimeSelect' => 'Common\Form\View\Helper\FormDateTimeSelect',
            'formDateSelect' => \Common\Form\View\Helper\FormDateSelect::class,
            FormInputSearch::class => FormInputSearch::class,
            'formPlainText' => 'Common\Form\View\Helper\FormPlainText',
            'addTags' => 'Common\View\Helper\AddTags',
            'transportManagerApplicationStatus' => 'Common\View\Helper\TransportManagerApplicationStatus',
            'status' => 'Common\View\Helper\Status',
            'address' => 'Common\View\Helper\Address',
            'personName' => 'Common\View\Helper\PersonName',
            'dateTime' => \Common\View\Helper\DateTime::class,
            'returnToAddress' => Common\View\Helper\ReturnToAddress::class,
            'navigationParentPage' => Common\View\Helper\NavigationParentPage::class,
            'panel' => Panel::class,
            'link' => Common\View\Helper\Link::class,
            'linkNewWindow' => Common\View\Helper\LinkNewWindow::class,
            'linkNewWindowExternal' => Common\View\Helper\LinkNewWindowExternal::class,
            'linkModal' => Common\View\Helper\LinkModal::class,

            //  read only elements helpers
            ReadonlyFormHelper\FormFieldset::class => ReadonlyFormHelper\FormFieldset::class,
            ReadonlyFormHelper\FormFileUploadList::class => ReadonlyFormHelper\FormFileUploadList::class,
            'readonlyformitem' => ReadonlyFormHelper\FormItem::class,
            'readonlyformselect' => ReadonlyFormHelper\FormSelect::class,
            'readonlyformdateselect' => ReadonlyFormHelper\FormDateSelect::class,
            'readonlyformrow' => ReadonlyFormHelper\FormRow::class,
            'readonlyformtable' => ReadonlyFormHelper\FormTable::class,
            'readOnlyActions' => \Common\View\Helper\ReadOnlyActions::class,

            'currencyFormatter' => \Common\View\Helper\CurrencyFormatter::class,

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
        ],
        'factories' => [
            'applicationName' => \Common\View\Helper\ApplicationNameFactory::class,
            'config' => \Common\View\Helper\ConfigFactory::class,
            'version' => \Common\View\Helper\VersionFactory::class,
            'pageId' => \Common\View\Helper\PageIdFactory::class,
            'pageTitle' => \Common\View\Helper\PageTitleFactory::class,
            'LicenceChecklist' => \Common\View\Helper\LicenceChecklistFactory::class,
            'date' => \Common\View\Helper\DateFactory::class,
            'formRow' => \Common\Form\View\Helper\FormRowFactory::class,
            'languageLink' => \Common\View\Helper\LanguageLinkFactory::class,
            'currentUser' => \Common\View\Helper\CurrentUserFactory::class,
            'systemInfoMessages' => \Common\View\Factory\Helper\SystemInfoMessagesFactory::class,
            'linkBack' => Common\View\Helper\LinkBackFactory::class,
            'translateReplace' => \Common\View\Helper\TranslateReplaceFactory::class,
            'flashMessengerAll' => \Common\View\Factory\Helper\FlashMessengerFactory::class,
            'escapeHtml' => \Common\View\Factory\Helper\EscapeHtmlFactory::class,
            \Common\Form\View\Helper\FormElementErrors::class => \Common\Form\View\Helper\FormElementErrorsFactory::class,
            \Common\Form\View\Helper\FormErrors::class => \Common\Form\View\Helper\FormErrorsFactory::class,
            \Common\Form\View\Helper\FormElement::class => \Common\Form\View\Helper\FormElementFactory::class,
        ],
        'aliases' => [
            'formElement' => \Common\Form\View\Helper\FormElement::class,
            'formElementErrors' => \Common\Form\View\Helper\FormElementErrors::class,
            'formelementerrors' => \Common\Form\View\Helper\FormElementErrors::class,
            'formErrors' => \Common\Form\View\Helper\FormErrors::class,
            'formerrors' => \Common\Form\View\Helper\FormErrors::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'partials/view' => __DIR__ . '/../view',
            'translations' => __DIR__ . '/../config/language/partials'
        ]
    ],
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
            DynamicSelect::class => DynamicSelectFactory::class,
            DynamicMultiCheckbox::class => DynamicMultiCheckboxFactory::class,
            DynamicRadio::class => DynamicRadioFactory::class,
            DynamicRadioHtml::class => DynamicRadioHtmlFactory::class,
        ],
        'aliases' => [
            'DynamicSelect' => DynamicSelect::class,
            'DynamicMultiCheckbox' => DynamicMultiCheckbox::class,
            'DynamicRadio' => DynamicRadio::class,
            'DynamicRadioHtml' => DynamicRadioHtml::class,
            'OlcsCheckbox' => OlcsCheckbox::class
        ]
    ],
    'validation' => [

        /**
         * Configures which message templates should have their default message templates replaced.
         *
         * Entries should map a validation message key to the validator class that yields the validation messages for
         * that key
         *
         * "validation message key" => "validator class reference"
         *
         * @type array
         */
        'default_message_templates_to_replace' => [
            \Laminas\Validator\NotEmpty::IS_EMPTY => \Laminas\Validator\NotEmpty::class,
        ],
    ],
    'validators' => [
        'invokables' => [
            'Common\Validator\ValidateIf' => 'Common\Validator\ValidateIf',
            'Common\Validator\ValidateIfMultiple' => 'Common\Validator\ValidateIfMultiple',
            'Common\Validator\DateCompare' => 'Common\Validator\DateCompare',
            'Common\Validator\NumberCompare' => 'Common\Validator\NumberCompare',
            'Common\Validator\SumCompare' => 'Common\Validator\SumCompare',
            'Common\Form\Elements\Validators\DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'Common\Validator\OneOf' => 'Common\Validator\OneOf',
            'Common\Form\Elements\Validators\Date' => 'Common\Form\Elements\Validators\Date',
            'Common\Validator\DateInFuture' => 'Common\Validator\DateInFuture',
            'Common\Validator\DateCompareWithInterval' => 'Common\Validator\DateCompareWithInterval',
            'Common\Validator\FileUploadCount' => 'Common\Validator\FileUploadCount',
        ],
        'aliases' => [
            'ValidateIf' => 'Common\Validator\ValidateIf',
            'ValidateIfMultiple' => 'Common\Validator\ValidateIfMultiple',
            'DateCompare' => 'Common\Validator\DateCompare',
            'NumberCompare' => 'Common\Validator\NumberCompare',
            'SumCompare' => 'Common\Validator\SumCompare',
            'DateNotInFuture' => 'Common\Form\Elements\Validators\DateNotInFuture',
            'OneOf' => 'Common\Validator\OneOf',
            'Date' => 'Common\Form\Elements\Validators\Date',
            'DateInFuture' => 'Common\Validator\DateInFuture',
            'DateCompareWithInterval' => 'Common\Validator\DateCompareWithInterval',
        ],
        'factories' => [
            QaService\DateNotInPastValidator::class => QaService\DateNotInPastValidatorFactory::class,
            QaService\Custom\Common\DateBeforeValidator::class => QaService\Custom\Common\DateBeforeValidatorFactory::class,
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
        'factories' => [
            DataService\ApplicationOperatingCentre::class => DataService\ApplicationOperatingCentreFactory::class,
            DataService\LicenceOperatingCentre::class => DataService\LicenceOperatingCentreFactory::class,
            DataService\OcContextListDataService::class => DataService\OcContextListDataServiceFactory::class,
            DataService\Venue::class => DataService\VenueFactory::class,
            DataService\Search\Search::class => DataService\Search\SearchFactory::class,
            SearchType::class => SearchType::class,
        ]
    ],
    'tables' => [
        'config' => [
            __DIR__ . '/../src/Common/Table/Tables/'
        ],
        'partials' => [
            'html' => __DIR__ . '/../view/table/',
            'csv' => __DIR__ . '/../view/table/csv'
        ]
    ],
    'fieldsets_path' => __DIR__ . '/../../Common/src/Common/Form/Fieldsets/',
    'static-list-data' => include __DIR__ . '/list-data/static-list-data.php',
    'form' => [
        'element' => [
            'renderers' => [
                \Common\Form\Elements\Custom\RadioVertical::class => \Common\Form\View\Helper\FormRadioVertical::class
            ],
        ],
    ],
    'rest_services' => [
        'abstract_factories' => [
            'Common\Service\Api\AbstractFactory'
        ]
    ],
    'service_api_mapping' => [
        'endpoints' => [
            'payments' => 'http://olcspayment.dev/api/',
            'backend' => 'http://olcs-backend/',
            'postcode' => 'http://postcode.cit.olcs.mgt.mtpdvsa/',
        ]
    ],
    'zfc_rbac' => [
        'identity_provider' => IdentityProviderInterface::class,
        'role_provider' => [\Common\Rbac\Role\RoleProvider::class => []],
        'role_provider_manager' => [
            'factories' => [
                \Common\Rbac\Role\RoleProvider::class => \Common\Rbac\Role\RoleProviderFactory::class
            ]
        ],
        'protection_policy' => \ZfcRbac\Guard\GuardInterface::POLICY_DENY,
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
            'lva-licence-addperson' => LvaFormService\People\LicenceAddPerson::class,
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
            'lva-variation-licence_history' => LvaFormService\LicenceHistory::class,

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

            // Continuation forms
            'continuations-checklist' => ContinuationFormService\LicenceChecklist::class,
            'continuations-start' => ContinuationFormService\Start::class,
            'continuations-payment' => ContinuationFormService\Payment::class,
            ContinuationFormService\Declaration::class => ContinuationFormService\Declaration::class,
            ContinuationFormService\ConditionsUndertakings::class =>
                ContinuationFormService\ConditionsUndertakings::class,
        ]
    ],
    'translator_plugins' => [
        'factories' => [
            TranslationLoader::class => TranslationLoaderFactory::class
        ],
    ],
    'translator' => [
        'locale' => [
            'en_GB', //default locale
            'en_GB', //fallback locale
        ],
        'remote_translation' => [
            [
                'type' => TranslationLoader::class,
            ]
        ],
    ],
];
