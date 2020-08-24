<?php

/**
 * Language Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\View\Helper;

use Common\Preference\Language;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
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
        $this->viewHelper = new LanguageLink();

        $this->sm = Bootstrap::getServiceManager();
        $this->sm->shouldReceive('getServiceLocator')
            ->andReturnSelf();
    }

    public function testInvoke()
    {
        $lanPref = m::mock();
        $lanPref->shouldReceive('getPreference')
            ->andReturn(Language::OPTION_CY);

        $this->sm->setService('LanguagePreference', $lanPref);

        $helper = $this->viewHelper;

        $this->viewHelper->createService($this->sm);
        $this->assertEquals('<a class="govuk-footer__link" href="?lang=en">English</a>', $helper());
    }

    public function testInvokeEnglish()
    {
        $lanPref = m::mock();
        $lanPref->shouldReceive('getPreference')
            ->andReturn(Language::OPTION_EN);

        $this->sm->setService('LanguagePreference', $lanPref);

        $helper = $this->viewHelper;

        $this->viewHelper->createService($this->sm);
        $this->assertEquals('<a class="govuk-footer__link" href="?lang=cy">Cymraeg</a>', $helper());
    }
}
