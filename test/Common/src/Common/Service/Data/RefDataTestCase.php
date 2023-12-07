<?php

namespace CommonTest\Service\Data;

use Common\Preference\Language as LanguagePreference;
use Common\Service\Data\RefDataServices;
use CommonTest\Common\Service\Data\AbstractListDataServiceTestCase;
use Mockery as m;

/**
 * RefDataTestCase
 */
class RefDataTestCase extends AbstractListDataServiceTestCase
{
    /** @var  RefDataServices */
    protected $refDataServices;

    /** @var LanguagePreference */
    protected $languagePreferenceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->languagePreferenceService = m::mock(LanguagePreference::class);

        $this->refDataServices = new RefDataServices(
            $this->abstractListDataServiceServices,
            $this->languagePreferenceService
        );
    }
}
