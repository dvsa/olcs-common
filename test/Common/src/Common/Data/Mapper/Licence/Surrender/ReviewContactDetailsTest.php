<?php


namespace CommonTest\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\ReviewContactDetails;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Url;

class ReviewContactDetailsTest extends AbstractReviewContactDetailsTest
{
    public function testMakeSections()
    {
        $mockTranslator = m::mock(TranslationHelperService::class);
        $mockUrlHelper = m::mock(Url::class);
        $mockLicence = $this->mockLicence();

        $this->mockTranslatorForContactDetails($mockTranslator);
        $this->mockTranslatorForCorrespondenceAddress($mockTranslator);
        $this->mockTranslatorForLicenceDetails($mockTranslator);

        $this->mockUrlHelperFromRoute($mockUrlHelper, 'licence/surrender/address-details', 2);

        $sections = ReviewContactDetails::makeSections($mockLicence, $mockUrlHelper, $mockTranslator);

        $expected = [
            $this->expectedForLicenceDetails(),
            $this->expectedForCorrespondenceAddress(),
            $this->expectedForContactDetails(),
        ];

        $this->assertSame($expected, $sections);
    }
}
