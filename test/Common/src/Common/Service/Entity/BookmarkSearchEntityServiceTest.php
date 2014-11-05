<?php

/**
 * Bookmark Search Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\BookmarkSearchEntityService;

/**
 * Bookmark Search Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BookmarkSearchEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new BookmarkSearchEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testSearchQuery()
    {
        $bundle = array(
            'foo' => 'bar'
        );

        $this->expectOneRestCall('BookmarkSearch', 'GET', [], $bundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->searchQuery($bundle));
    }
}
