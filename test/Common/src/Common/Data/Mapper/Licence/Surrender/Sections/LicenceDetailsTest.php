<?php


namespace CommonTest\Data\Mapper\Licence\Surrender\Sections;

use Common\Data\Mapper\Licence\Surrender\Sections\LicenceDetails;
use Common\Service\Helper\TranslationHelperService;
use CommonTest\Data\Mapper\Licence\Surrender\AbstractReviewContactDetailsTest;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Url;

class LicenceDetailsTest extends AbstractReviewContactDetailsTest
{
    public function testMakeQuestions()
    {
        $mockTranslator = m::mock(TranslationHelperService::class);
        $mockUrlHelper = m::mock(Url::class);
        $this->mockTranslatorForLicenceDetails($mockTranslator);

        $mockLicence = $this->mockLicence();

        $sut = new LicenceDetails($mockLicence, $mockUrlHelper, $mockTranslator);
        $section = $sut->makeSection();

        $expected = $this->expectedForLicenceDetails($mockLicence);

        $this->assertSame($expected, $section);
    }
}
