<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\TmDecisionText1;
use Common\Data\Object\Publication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TmDecisionText1Test
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmDecisionText1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     */
    public function testFilter()
    {
        $pi = 1;
        $piVenueOther = 'Pi Venue Information';
        $hearingDate = '12 May 2014';
        $previousHearingDate = '3 April 2014';
        $hearingTime = '14:30';
        $previousPublication = 6830;
        $caseId = 84;
        $publicationData = [
            'pi' => $pi,
            'case' => [
                'id' => $caseId,
                'transportManager' => [
                    'id' => 3
                ]
            ],
            'transportManagerName' => 'Mr John Smith',
            'hearingData' => [
                'id' => $pi,
                'piVenueOther' => $piVenueOther,
                'date' => $hearingDate,
                'time' => $hearingTime,
                'previousPublication' => $previousPublication,
                'previousHearing' => [
                    'isAdjourned' => true,
                    'date' => $previousHearingDate
                ]
            ]
        ];

        $expectedOutput = sprintf(
            'TM Public Inquiry (Case ID: %s, Public Inquiry ID: %s) for %s held at %s,
    on %s at %s (Previous Publication:'
            . '(%s)) Previous hearing on %s was adjourned.',
            $caseId,
            $pi,
            $publicationData['transportManagerName'],
            $piVenueOther,
            $hearingDate,
            $hearingTime,
            $previousPublication,
            $previousHearingDate
        );

        $input = new Publication($publicationData);
        $sut = new TmDecisionText1();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('text1'));
    }
}
