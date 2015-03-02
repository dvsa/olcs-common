<?php

/**
 * Note Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\NoteEntityService;

/**
 * Note Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class NoteEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new NoteEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetNote()
    {
        $id = 99;

        $response = array('RESPONSE');

        $this->expectOneRestCall('Note', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->getNote($id));
    }


    /**
     * @group entity_services
     */
    public function testGetNotesList()
    {
        $filters = ['noteType' => 'foo'];

        $response = array('RESPONSE');

        $this->expectOneRestCall('Note', 'GET', $filters)
            ->will($this->returnValue(['Results' => ['NOTES']]));

        $this->assertEquals(['NOTES'], $this->sut->getNotesList($filters));
    }
}
