<?php

namespace CommonTest\View\Helper;

use Common\Preference\Language;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\LanguageLink;

/**
 * Language Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LanguageLinkTest extends MockeryTestCase
{
    /**
     * @var LanguageLink
     */
    protected $viewHelper;

    protected $sm;

    public function setUp(): void
    {
        $languagePref = m::mock(Language::class);
        $this->languagePref = $languagePref;

        $this->viewHelper = new LanguageLink($languagePref);

        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $this->sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

    }

    public function testInvoke()
    {
        $this->languagePref->shouldReceive('getPreference')
            ->andReturn(Language::OPTION_CY);

        $this->assertEquals('<a class="govuk-footer__link" href="?lang=en">English</a>', $this->viewHelper->__invoke());
    }

    public function testInvokeEnglish()
    {
        $this->languagePref->shouldReceive('getPreference')
            ->andReturn(Language::OPTION_EN);

        $helper = $this->viewHelper;

        $this->assertEquals('<a class="govuk-footer__link" href="?lang=cy">Cymraeg</a>', $helper());
    }
}
