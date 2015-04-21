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
     * Test fetchDocuments for no localAuthority or organisation
     * @group entity_services
     */
    public function testFetchBusRegDocumentsNoLocalAuthority()
    {
        $params = [
            'busReg' => 123,
            'sort' => 'createdOn',
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

        $result = $this->sut->fetchBusRegDocuments($params['busReg']);

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
            'sort' => 'createdOn',
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
            'organisation' => 6,
            'sort' => 'createdOn',
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

        $result = $this->sut->fetchBusRegDocuments($params['busReg'], null, ['id' => $params['organisation']]);

        $this->assertCount(3, $result);
        $this->assertTrue(in_array('RD', $result));
        $this->assertTrue(in_array('PD', $result));
        $this->assertTrue(in_array('ZD', $result));
    }
}
