<?php

/**
 * Cases Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CasesEntityService;

/**
 * Cases Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CasesEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CasesEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifier()
    {
        $this->expectOneRestCall('Cases', 'GET', 123)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testGetOpenForLicence()
    {
        $this->expectOneRestCall(
            'Cases',
            'GET',
            [
                'licence' => 123,
                'closeDate' => 'NULL',
                'deletedDate' => 'NULL',
                'limit' => 'all'
            ]
        )->will($this->returnValue(['Results' => ['CASES']]));

        $this->assertEquals(['CASES'], $this->sut->getOpenForLicence(123));
    }
}
