<?php

/**
 * Community lic Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CommunityLicEntityService;

/**
 * Community lic Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLicEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CommunityLicEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetList()
    {
        $query = [
            'foo' => 'bar'
        ];

        $this->expectOneRestCall('CommunityLic', 'GET', $query, ['children' => ['status']])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getList($query));
    }
}
