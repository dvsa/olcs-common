<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DataRetentionRecordLink;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * DataRetentionRecord Link test
 */
class DataRetentionRecordLinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array  $queryData           Query Data
     * @param string $routeName           Expected route name
     * @param array  $routeParameters     Expected route parameters
     * @param bool   $isHyperLinkExpected Is hyperlink expected in string
     *
     * @dataProvider entityTypeDataProvider
     */
    public function testFormat(
        $queryData,
        $routeName,
        $routeParameters,
        $isHyperLinkExpected
    ) {
        $queryData = array_merge(
            [
                'organisationName' => 'DVSA',
                'organisationId' => 'ORG123',
                'licNo' => 'OB1234',
                'licenceId' => '9',
            ],
            $queryData
        );

        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        $routeName,
                        $routeParameters
                    )
                    ->andReturn('DATA_RETENTION_RECORD_URL')
                    ->getMock()
            )
            ->getMock();

        if ($isHyperLinkExpected) {
            $this->assertEquals(
                sprintf(
                    '<a href="%s" target="_self">%s</a>',
                    'DATA_RETENTION_RECORD_URL',
                    $queryData['organisationName']
                ) . ' / ' .
                sprintf(
                    '<a href="%s" target="_self">%s</a>',
                    'DATA_RETENTION_RECORD_URL',
                    $queryData['licNo']
                ) . ' / ' .
                sprintf(
                    '<a href="%s" target="_self">%s</a>',
                    'DATA_RETENTION_RECORD_URL',
                    ucfirst($queryData['entityName'])
                ) . ' / ' .
                sprintf(
                    '<a href="%s" target="_self">%s</a>',
                    'DATA_RETENTION_RECORD_URL',
                    $queryData['entityPk']
                ),
                DataRetentionRecordLink::format($queryData, [], $sm)
            );
        } else {
            $this->assertEquals(
                $queryData['organisationName'] . ' / ' .
                $queryData['licNo'] . ' / ' .
                $queryData['entityName'] . ' / ' .
                $queryData['entityPk'],
                DataRetentionRecordLink::format($queryData, [], $sm)
            );
        }
    }

    /**
     * Parameter 1: data from query
     * Parameter 2: expected route name
     * Parameter 3: expected route parameters
     * Parameter 4: is hyperlink expected? (boolean)
     *
     * @return array
     */
    public function entityTypeDataProvider()
    {
        return [
            'Licence entity type' => [
                ['entityName' => 'licence', 'entityPk' => 3],
                'licence',
                ['licence' => 3],
                true,
            ],
            'Application entity type' => [
                ['entityName' => 'application', 'entityPk' => 5],
                'lva-application',
                ['application' => 5],
                true,
            ],
            'Transport manager entity type' => [
                ['entityName' => 'transport_manager', 'entityPk' => 1],
                'transport-manager',
                ['transport-manager' => 1],
                true,
            ],
            'IRFO GV Permit' => [
                ['entityName' => 'irfo_gv_permit', 'entityPk' => 6],
                'operator/irfo/gv-permits',
                ['organisation' => 'ORG123'],
                true,
            ],
            'IRFO PSV Permit' => [
                ['entityName' => 'irfo_psv_auth', 'entityPk' => 7],
                'operator/irfo/gv-permits',
                ['organisation' => 'ORG123'],
                true,
            ],
            'Organisation' => [
                ['entityName' => 'organisation', 'entityPk' => 8],
                'operator/business-details',
                ['organisation' => 'ORG123'],
                true,
            ],
            'Cases' => [
                ['entityName' => 'cases', 'entityPk' => 10],
                'case',
                ['action' => 'details', 'case' => 10],
                true,
            ],
            'Bus reg' => [
                ['entityName' => 'bus_reg', 'entityPk' => 11],
                'licence/bus-details',
                ['licence' => 9, 'busRegId' => 11],
                true,
            ],
            'Undefined' => [
                ['entityName' => 'undefined', 'entityPk' => 1],
                null,
                [],
                false,
            ]
        ];
    }

    public function testWithoutLicenceNumberAndUndefinedEntity()
    {
        $sm = m::mock(ServiceLocatorInterface::class)
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->getMock();

        $queryData = [
            'entityName' => 'undefined',
            'organisationName' => 'DVSA',
            'entityPk' => 3,
            'licenceId' => 9,
            'licNo' => null,
        ];

        $this->assertEquals(
            $queryData['organisationName'] . ' / ' .
            $queryData['entityName'] . ' / ' .
            $queryData['entityPk'],
            DataRetentionRecordLink::format($queryData, [], $sm)
        );
    }
}
