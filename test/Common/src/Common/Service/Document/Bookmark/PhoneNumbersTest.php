<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\PhoneNumbers;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Phone Numbers bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PhoneNumbersTest extends MockeryTestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new PhoneNumbers();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoPhoneNumbers()
    {
        $bookmark = new PhoneNumbers();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithPhoneNumbers()
    {
        $bookmark = m::mock('Common\Service\Document\Bookmark\PhoneNumbers')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('CHECKLIST_2CELL_TABLE')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'organisation' => [
                    'correspondenceCd' => [
                        'phoneContacts' => [
                            [
                                'phoneNumber' => '123',
                                'phoneContactType' => [
                                    'id' => 'phone_t_mobile',
                                    'description' => 'mobile'
                                ]
                            ],
                            [
                                'phoneNumber' => '456',
                                'phoneContactType' => [
                                    'id' => 'phone_t_home',
                                    'description' => 'home'
                                ]
                            ],
                            [
                                'phoneNumber' => '012',
                                'phoneContactType' => [
                                    'id' => 'phone_t_fax',
                                    'description' => 'fax'
                                ]
                            ],
                            [
                                'phoneNumber' => '789',
                                'phoneContactType' => [
                                    'id' => 'phone_t_fax',
                                    'description' => 'fax'
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        );

        $header1 = [
            'BOOKMARK1' => 'Phone number(s)',
            'BOOKMARK2' => ''
        ];
        $header2 = [
            'BOOKMARK1' => 'Type of contact number',
            'BOOKMARK2' => 'Number'
        ];
        $row1 = [
            'BOOKMARK1' => 'home',
            'BOOKMARK2' => '456'
        ];
        $row2 = [
            'BOOKMARK1' => 'mobile',
            'BOOKMARK2' => '123'
        ];
        $row3 = [
            'BOOKMARK1' => 'fax',
            'BOOKMARK2' => '789'
        ];
        $row4 = [
            'BOOKMARK1' => 'fax',
            'BOOKMARK2' => '012'
        ];
        $emptyRow = [
            'BOOKMARK1' => '',
            'BOOKMARK2' => ''
        ];

        $mockParser = m::mock('Common\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $header1)
            ->andReturn('header1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $header2)
            ->andReturn('header2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row1)
            ->andReturn('row1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row2)
            ->andReturn('row2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row3)
            ->andReturn('row3|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row4)
            ->andReturn('row4|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $emptyRow)
            ->andReturn('emptyrow|')
            ->once()
            ->getMock();

        $bookmark->setParser($mockParser);

        $rendered = 'header1|header2|row1|row2|row3|row4|emptyrow|';
        $this->assertEquals(
            $rendered,
            $bookmark->render()
        );
    }
}
