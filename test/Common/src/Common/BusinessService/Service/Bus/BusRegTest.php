<?php

/**
 * Bus Reg Test
 */
namespace CommonTest\BusinessService\Service\Bus;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Bus\BusReg;
use Common\Service\Entity\FeeEntityService;

/**
 * Bus Reg Test
 */
class BusRegTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new BusReg();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider isGrantableDataProvider
     */
    public function testIsGrantable($busRegData, $feeData, $expectedResult)
    {
        // Data
        $busRegId = 1;

        // Mocks
        $mockBusRegEntity = m::mock('Common\Service\Data\Interfaces\Updateable');
        $mockFeeEntity = m::mock('Common\Service\Data\Interfaces\Updateable');

        // Expectations
        $mockBusRegEntity->shouldReceive('getDataForGrantable')->with($busRegId)->andReturn($busRegData);
        $this->sm->setService('Entity\BusReg', $mockBusRegEntity);

        $mockFeeEntity->shouldReceive('getLatestFeeForBusReg')->with($busRegId)->andReturn($feeData);
        $this->sm->setService('Entity\Fee', $mockFeeEntity);

        $this->assertEquals($expectedResult, $this->sut->isGrantable($busRegId));
    }

    public function isGrantableDataProvider()
    {
        $minimumGrantableBusRegData = [
            'timetableAcceptable' => 'Y',
            'mapSupplied' => 'Y',
            'trcConditionChecked' => 'Y',
            'copiedToLaPte' => 'Y',
            'laShortNote' => 'Y',
            'applicationSigned' => 'Y',
            'effectiveDate' => 'any value',
            'receivedDate' => 'any value',
            'serviceNo' => 'any value',
            'startPoint' => 'any value',
            'finishPoint' => 'any value',
            'busServiceTypes' => ['any value'],
            'trafficAreas' => ['any value'],
            'localAuthoritys' => ['any value'],
            'isShortNotice' => 'N',
        ];

        return [
            // Grantable - Rule: Other - isShortNotice: N - Fee: none
            [
                $minimumGrantableBusRegData,
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // timetableAcceptable: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['timetableAcceptable' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // mapSupplied: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['mapSupplied' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // trcConditionChecked: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['trcConditionChecked' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // copiedToLaPte: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['copiedToLaPte' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // laShortNote: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['laShortNote' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // applicationSigned: N
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['applicationSigned' => 'N']
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // effectiveDate empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['effectiveDate' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // receivedDate empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['receivedDate' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // serviceNo empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['serviceNo' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // startPoint empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['startPoint' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // finishPoint empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['finishPoint' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // busServiceTypes empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['busServiceTypes' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // trafficAreas empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['trafficAreas' => null]
                ),
                [],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
            // localAuthoritys empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['localAuthoritys' => null]
                ),
                [],
                false
            ],
            // Grantable - Rule: Scotland - isShortNotice: N - Fee: none
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['busNoticePeriod' => ['id' => 1], 'opNotifiedLaPte' => 'Y']
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Scotland - isShortNotice: N - Fee: none
            // extra data required from Scotland missing
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['busNoticePeriod' => ['id' => 1]]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: N - Fee: paid
            [
                $minimumGrantableBusRegData,
                ['feeStatus' => ['id' => FeeEntityService::STATUS_PAID]],
                true
            ],
            // Grantable - Rule: Other - isShortNotice: N - Fee: waived
            [
                $minimumGrantableBusRegData,
                ['feeStatus' => ['id' => FeeEntityService::STATUS_WAIVED]],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: N - Fee: outstanding
            [
                $minimumGrantableBusRegData,
                ['feeStatus' => ['id' => FeeEntityService::STATUS_OUTSTANDING]],
                false
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // missing short notice details
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    ['isShortNotice' => 'Y']
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // bankHolidayChange: Y
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['bankHolidayChange' => 'Y']
                    ]
                ),
                [],
                true
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // connectionChange: Y, connectionDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['connectionChange' => 'Y', 'connectionDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // connectionChange: Y, connectionDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['connectionChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // holidayChange: Y, holidayDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['holidayChange' => 'Y', 'holidayDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // holidayChange: Y, holidayDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['holidayChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // notAvailableChange: Y, notAvailableDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['notAvailableChange' => 'Y', 'notAvailableDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // notAvailableChange: Y, notAvailableDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['notAvailableChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // policeChange: Y, policeDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['policeChange' => 'Y', 'policeDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // policeChange: Y, policeDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['policeChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // replacementChange: Y, replacementDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['replacementChange' => 'Y', 'replacementDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // replacementChange: Y, replacementDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['replacementChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // specialOccasionChange: Y, specialOccasionDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['specialOccasionChange' => 'Y', 'specialOccasionDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // specialOccasionChange: Y, specialOccasionDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['specialOccasionChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // timetableChange: Y, timetableDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['timetableChange' => 'Y', 'timetableDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // timetableChange: Y, timetableDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['timetableChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // trcChange: Y, trcDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['trcChange' => 'Y', 'trcDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // trcChange: Y, trcDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['trcChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
            // Grantable - Rule: Other - isShortNotice: Y - Fee: none
            // unforseenChange: Y, unforseenDetail: not empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['unforseenChange' => 'Y', 'unforseenDetail' => 'any value']
                    ]
                ),
                [],
                true
            ],
            // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
            // unforseenChange: Y, unforseenDetail: empty
            [
                array_merge(
                    $minimumGrantableBusRegData,
                    [
                        'isShortNotice' => 'Y',
                        'shortNotice' => ['unforseenChange' => 'Y']
                    ]
                ),
                [],
                false
            ],
        ];
    }
}
