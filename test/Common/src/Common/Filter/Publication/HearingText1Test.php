<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\HearingText1;
use Common\Data\Object\Publication;

/**
 * Class HearingText1Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingText1Test extends \PHPUnit_Framework_TestCase
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

        $publicationData = [
            'pi' => $pi,
            'licenceAddress' => $licenceAddress,
            'licenceData' => [
                'licNo' => $licNo,
                'licenceType' => [
                    'olbsKey' => $licenceType
                ],
                'organisation' => [
                    'name' => $organisationName,
                    'type' => [
                        'id' => $organisationType
                    ],
                    'tradingNames' => [
                        0 => [
                            'name' => $organisationTradingName
                        ]
                    ],
                    'organisationPersons' => [
                        0 => [
                            'person' => $personData
                        ]
                    ]
                ]
            ],
            'hearingData' => [
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
            'Public Inquiry (%s) to be held at %s, on %s commencing at %s (Previous Publication:'
            . '(%s)) Previous hearing on %s was adjourned. '
            . "\n" . '%s %s '
            . "\n" . '%s'
            . "\n" . 'T/A %s '
            . "\n" . '%s '
            . "\n" . '%s',
            $pi,
            $piVenueOther,
            $hearingDate,
            $hearingTime,
            $previousPublication,
            $previousHearingDate,
            $licNo,
            $licenceType,
            strtoupper($publicationData['licenceData']['organisation']['name']),
            strtoupper($publicationData['licenceData']['organisation']['tradingNames'][0]['name']),
            $personPrefix . strtoupper($personName),
            strtoupper($licenceAddress)
        );

        $input = new Publication($publicationData);
        $sut = new HearingText1();

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
