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
     * @group entity_services
     */
    public function testFetchBusRegDocuments()
    {
        $params = [
            'busReg' => 123,
            'localAuthority' => 'NULL'
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
}
