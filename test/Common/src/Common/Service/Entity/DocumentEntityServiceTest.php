<?php

/**
 * Document Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\DocumentEntityService;

/**
 * Document Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new DocumentEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetIdentifier()
    {
        $id = 3;
        $response = array(
            'identifier' => 1
        );

        $this->expectOneRestCall('Document', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(1, $this->sut->getIdentifier($id));
    }

    /**
     * @group entity_services
     */
    public function testGetIdentifierWithoutIdentifier()
    {
        $id = 3;
        $response = array(
            'foo' => 'bar'
        );

        $this->expectOneRestCall('Document', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertNull($this->sut->getIdentifier($id));
    }

    /**
     * @group entity_services
     */
    public function testGetIdentifierWithEmptyIdentifier()
    {
        $id = 3;
        $response = array(
            'identifier' => ''
        );

        $this->expectOneRestCall('Document', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertNull($this->sut->getIdentifier($id));
    }
}
