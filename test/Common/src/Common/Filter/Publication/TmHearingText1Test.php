<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\TmHearingText1;
use Common\Data\Object\Publication;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TmHearingText1Test
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmHearingText1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * @dataProvider filterProvider
     *
     * @param string $organisationType
     * @param string $personPrefix
     */
    public function testFilter($organisationType, $personPrefix)
    {
        $pi = 1;
        $licenceAddress = 'line 1, line 2, line 3, line 4, town, postcode';
        $licNo = 'OB1234567';
        $licenceType = 'SN';
        $piVenueOther = 'Pi Venue Information';
        $hearingDate = '12 May 2014';
        $previousHearingDate = '3 April 2014';
        $hearingTime = '14:30';
        $organisationName = 'Organisation Name';
        $organisationTradingName = 'Organisation Trading Name';
        $previousPublication = 6830;
        $personData = [
            'forename' => 'John',
            'familyName' => 'Smith',
        ];
        $personName = $personData['forename'] . ' ' . $personData['familyName'];
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
            'TM Public Inquiry (Case ID: %s, Public Inquiry ID: %s) for %s to be held at %s,
    on %s commencing at %s (Previous Publication:'
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
        $sut = new TmHearingText1();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('text1'));
    }

    public function filterProvider()
    {
        return [
            ['org_t_rc', 'Director(s): '],
            ['org_t_llp', 'Partner(s): '],
            ['', '']
        ];
    }
}
