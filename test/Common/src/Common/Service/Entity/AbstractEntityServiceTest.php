<?php

/**
 * Abstract Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

/**
 * Abstract Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = $this->getMockForAbstractClass('\Common\Service\Entity\AbstractEntityService');

        parent::setUp();
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Exception\ConfigurationException
     */
    public function testSaveWithoutDefiningEntity()
    {
        $data = array();

        $this->sut->save($data);
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Exception\ConfigurationException
     */
    public function testDeleteWithoutDefiningEntity()
    {
        $id = 1;

        $this->sut->delete($id);
    }

    /**
     * @group entity_services
     * @group entity_services_current
     */
    public function testDelete()
    {
        $id = 1;

        $this->setEntity('Foo');

        $this->expectOneRestCall('Foo', 'DELETE', array('id' => $id));

        $this->sut->delete($id);
    }
}
