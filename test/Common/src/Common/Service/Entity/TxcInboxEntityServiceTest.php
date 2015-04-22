<?php

/**
 * TxcInbox Entity Service Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TxcInboxEntityService;

/**
 * TxcInbox Entity Service Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class TxcInboxEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TxcInboxEntityService();

        parent::setUp();
    }

    /**
     * Test fetchDocuments for no localAuthority (same as testing for organisation)
     * @group entity_services
     */
    public function testFetchBusRegDocumentsNoLocalAuthority()
    {
        $restParams = [
            'busReg' => 123,
            'sort' => 'id',
            'order' => 'DESC',
            'localAuthority' => 'NULL',
        ];

        $mockDocs = [
            'Count' => 3,
            'Results' => [
                0 => [
                    'routeDocument' => 'RD',
                    'pdfDocument' => 'PD',
                    'zipDocument' => 'ZD'
                ]
            ]
        ];

        $this->expectOneRestCall('TxcInbox', 'GET', $restParams)
            ->will($this->returnValue($mockDocs));

        $result = $this->sut->fetchBusRegDocuments($restParams['busReg'], null);

        $this->assertCount(3, $result);
        $this->assertTrue(in_array('RD', $result));
        $this->assertTrue(in_array('PD', $result));
        $this->assertTrue(in_array('ZD', $result));
    }

    /**
     * Test fetchDocuments for localAuthority set
     *
     * @group entity_services
     */
    public function testFetchBusRegDocumentsForLocalAuthority()
    {
        $params = [
            'busReg' => 123,
            'localAuthority' => 4,
            'sort' => 'id',
            'order' => 'DESC',
            'fileRead' => 0
        ];
        $mockDocs = [
            'Count' => 3,
            'Results' => [
                0 => [
                    'routeDocument' => 'RD',
                    'pdfDocument' => 'PD',
                    'zipDocument' => 'ZD'
                ]
            ]
        ];

        $this->expectOneRestCall('TxcInbox', 'GET', $params)
            ->will($this->returnValue($mockDocs));

        $result = $this->sut->fetchBusRegDocuments($params['busReg'], ['id' => $params['localAuthority']]);

        $this->assertCount(3, $result);
        $this->assertTrue(in_array('RD', $result));
        $this->assertTrue(in_array('PD', $result));
        $this->assertTrue(in_array('ZD', $result));
    }

    /**
     * Test fetchDocuments for orgnaisation set
     *
     * @group entity_services
     */
    public function testFetchBusRegDocumentsForOrganisation()
    {
        $params = [
            'busReg' => 123,
            'sort' => 'id',
            'localAuthority' => 'NULL',
            'order' => 'DESC'
        ];

        $mockDocs = [
            'Count' => 3,
            'Results' => [
                0 => [
                    'routeDocument' => 'RD',
                    'pdfDocument' => 'PD',
                    'zipDocument' => 'ZD'
                ]
            ]
        ];

        $this->expectOneRestCall('TxcInbox', 'GET', $params)
            ->will($this->returnValue($mockDocs));

        $result = $this->sut->fetchBusRegDocuments($params['busReg'], null);

        $this->assertCount(3, $result);
        $this->assertTrue(in_array('RD', $result));
        $this->assertTrue(in_array('PD', $result));
        $this->assertTrue(in_array('ZD', $result));
    }
}
