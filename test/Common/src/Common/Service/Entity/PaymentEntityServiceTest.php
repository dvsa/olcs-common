<?php

/**
 * Payment Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PaymentEntityService;

/**
 * Payment Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PaymentEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PaymentEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetDetailsWithNoResults()
    {
        $query = array(
            'guid' => 1234,
            'limit' => 1
        );

        $results = [
            'Count' => 0,
            'Results' => []
        ];

        $this->expectOneRestCall('Payment', 'GET', $query)
            ->will($this->returnValue($results));

        $this->assertFalse($this->sut->getDetails(1234));
    }

    /**
     * @group entity_services
     */
    public function testGetDetailsWithOneResult()
    {
        $query = array(
            'guid' => 1234,
            'limit' => 1
        );

        $results = [
            'Count' => 1,
            'Results' => ['foo']
        ];

        $this->expectOneRestCall('Payment', 'GET', $query)
            ->will($this->returnValue($results));

        $this->assertEquals(
            'foo',
            $this->sut->getDetails(1234)
        );
    }

    /**
     * @group entity_services
     */
    public function testSetStatus()
    {
        $query = [
            'id' => 1,
            'status' => 'OK',
            '_OPTIONS_' => [
                'force' => true
            ]
        ];

        $this->expectOneRestCall('Payment', 'PUT', $query);

        $this->sut->setStatus(1, 'OK');
    }
}
