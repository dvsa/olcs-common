<?php

namespace CommonTest\Service\Qa\Custom\Bilateral;

use Common\Service\Qa\Custom\Bilateral\YesNoValueOptionsGenerator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * YesNoValueOptionsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class YesNoValueOptionsGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $yesCaption = 'yes.caption';
        $noCaption = 'no.caption';

        $expected = [
            'yes' => [
                'label' => $yesCaption,
                'value' => 'Y',
            ],
            'no' => [
                'label' => $noCaption,
                'value' => 'N',
            ]
        ];

        $yesNoValueOptionsGenerator = new YesNoValueOptionsGenerator();

        $this->assertEquals(
            $expected,
            $yesNoValueOptionsGenerator->generate($yesCaption, $noCaption)
        );
    }
}
