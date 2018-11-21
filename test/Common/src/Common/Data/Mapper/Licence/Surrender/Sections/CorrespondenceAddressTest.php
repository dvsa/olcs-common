<?php


namespace CommonTest\Data\Mapper\Licence\Surrender\Sections;

use Common\Data\Mapper\Licence\Surrender\Sections\CorrespondenceAddress;
use Common\Service\Helper\TranslationHelperService;
use CommonTest\Data\Mapper\Licence\Surrender\AbstractReviewContactDetailsTest;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Url;

class CorrespondenceAddressTest extends AbstractReviewContactDetailsTest
{
    public function testMakeQuestions()
    {
        $mockTranslator = m::mock(TranslationHelperService::class);
        $mockUrlHelper = m::mock(Url::class);
        $this->mockTranslatorForCorrespondenceAddress($mockTranslator);

        $this->mockUrlHelperFromRoute($mockUrlHelper, 'licence/surrender/address-details', 1);

        $mockLicence = $this->mockLicence();

        $sut = new CorrespondenceAddress($mockLicence, $mockUrlHelper, $mockTranslator);
        $section = $sut->makeSection();

        $expected = $this->expectedForCorrespondenceAddress($mockLicence);

        $this->assertSame($expected, $section);
    }
}