<?php

namespace Common\Service\Data;

use Common\Preference\Language as LanguagePreference;

/**
 * RefDataServices
 */
class RefDataServices
{
    /** @var AbstractListDataServiceServices */
    protected $abstractListDataServiceServices;

    /** @var LanguagePreference */
    protected $languagePreferenceService;

    /**
     * Create service instance
     *
     * @param AbstractListDataServiceServices $abstractListDataServiceServices
     * @param LanguagePreference $languagePreferenceService
     *
     * @return RefDataServices
     */
    public function __construct(
        AbstractListDataServiceServices $abstractListDataServiceServices,
        LanguagePreference $languagePreferenceService
    ) {
        $this->abstractListDataServiceServices = $abstractListDataServiceServices;
        $this->languagePreferenceService = $languagePreferenceService;
    }

    /**
     * Return the AbstractListDataServiceServices
     *
     * @return AbstractListDataServiceServices
     */
    public function getAbstractListDataServiceServices(): AbstractListDataServiceServices
    {
        return $this->abstractListDataServiceServices;
    }

    /**
     * Return the LanguagePreference service
     *
     * @return LanguagePreference
     */
    public function getLanguagePreferenceService(): LanguagePreference
    {
        return $this->languagePreferenceService;
    }
}
