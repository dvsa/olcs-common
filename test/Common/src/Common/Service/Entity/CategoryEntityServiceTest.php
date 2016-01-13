<?php

/**
 * Category Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CategoryEntityService;

/**
 * Category Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CategoryEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CategoryEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFindById()
    {
        $this->expectOneRestCall('Category', 'GET', 123)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findById(123));
    }
}
