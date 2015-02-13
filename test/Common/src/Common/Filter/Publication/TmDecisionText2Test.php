<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\TmDecisionText2;
use Common\Data\Object\Publication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TmDecisionText2Test
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmDecisionText2Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     */
    public function testFilter()
    {
        $pi = 1;
        $piVenueOther = 'Pi Venue Information';
        $DecisionDate = '12 May 2014';
        $previousDecisionDate = '3 April 2014';
        $DecisionTime = '14:30';
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
            'DecisionData' => [
                'id' => $pi,
                'piVenueOther' => $piVenueOther,
                'date' => $DecisionDate,
                'time' => $DecisionTime,
                'previousPublication' => $previousPublication,
                'previousDecision' => [
                    'isAdjourned' => true,
                    'date' => $previousDecisionDate
                ]
            ],
            'text2' => 'TEST TEXT 2'
        ];

        $input = new Publication($publicationData);
        $sut = new TmDecisionText2();

        $output = $sut->filter($input);

        $this->assertEquals($publicationData['text2'], $output->offsetGet('text2'));
    }
}
