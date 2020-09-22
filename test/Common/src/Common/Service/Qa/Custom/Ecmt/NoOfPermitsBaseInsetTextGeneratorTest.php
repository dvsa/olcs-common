<?php

namespace CommonTest\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Ecmt\NoOfPermitsBaseInsetTextGenerator;
use Common\View\Helper\CurrencyFormatter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsBaseInsetTextGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsBaseInsetTextGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $applicationFee = '10.00';
        $formattedApplicationFee = '£10';
        $issueFee = '17.00';
        $formattedIssueFee = '£17';

        $options = [
            'applicationFee' => $applicationFee,
            'issueFee' => $issueFee
        ];

        $translator = m::mock(TranslationHelperService::class);
        $translator->shouldReceive('translate')
            ->with('qanda.ecmt.number-of-permits.inset.base')
            ->andReturn('Formatted base text. Application fee %s, issue fee %s');

        $currencyFormatter = m::mock(CurrencyFormatter::class);
        $currencyFormatter->shouldReceive('__invoke')
            ->with($applicationFee)
            ->andReturn($formattedApplicationFee);
        $currencyFormatter->shouldReceive('__invoke')
            ->with($issueFee)
            ->andReturn($formattedIssueFee);

        $noOfPermitsBaseInsetTextGenerator = new NoOfPermitsBaseInsetTextGenerator($translator, $currencyFormatter);

        $expected = 'Formatted base text. Application fee £10, issue fee £17';

        $this->assertEquals(
            $expected,
            $noOfPermitsBaseInsetTextGenerator->generate($options)
        );
    }
}
