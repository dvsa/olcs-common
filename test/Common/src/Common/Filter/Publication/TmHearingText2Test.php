<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\TmHearingText2;
use Common\Data\Object\Publication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TmHearingText2Test
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmHearingText2Test extends MockeryTestCase
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
            ],
            'text2' => 'TEST TEXT 2'
        ];

        $input = new Publication($publicationData);
        $sut = new TmHearingText2();

        $output = $sut->filter($input);

        $this->assertEquals($publicationData['text2'], $output->offsetGet('text2'));
    }
}
