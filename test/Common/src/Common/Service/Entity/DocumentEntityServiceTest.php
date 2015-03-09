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

    /**
     * @group entity_services
     */
    public function testCreateFromFile()
    {

        $dateService = $this->getMock('\stdClass', ['getDate']);
        $dateService->expects($this->once())
            ->method('getDate')
            ->with('Y-m-d H:i:s')
            ->willReturn('2016-01-01 11:22:33');

        $this->sm->setService('Helper\Date', $dateService);

        $mock = $this->getMock('Common\Service\File\File');
        $mock->expects($this->once())
            ->method('getIdentifier')
            ->willReturn('id123');

        $mock->expects($this->once())
            ->method('getSize')
            ->willReturn(100);

        $input = [
            'foo' => 'bar'
        ];

        $data = [
            'identifier' => 'id123',
            'size' => 100,
            'issuedDate' => '2016-01-01 11:22:33',
            'foo' => 'bar'
        ];

        $this->expectOneRestCall('Document', 'POST', $data)
            ->will($this->returnValue('foo'));

        $this->assertEquals('foo', $this->sut->createFromFile($mock, $input));
    }
}
