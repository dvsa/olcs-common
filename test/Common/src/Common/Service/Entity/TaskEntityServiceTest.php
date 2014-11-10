<?php

/**
 * Task Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TaskEntityService;

/**
 * Task Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TaskEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testCloseByQueryWithNoResults()
    {
        $query = array(
            'id' => 1
        );

        $results = array(
            'Results' => array()
        );

        $this->expectOneRestCall('Task', 'GET', array('id' => 1, 'isClosed' => 'N', 'limit' => 'all'))
            ->will($this->returnValue($results));

        $this->sut->closeByQuery($query);
    }

    /**
     * @group entity_services
     */
    public function testCloseByQueryWithResult()
    {
        $query = array(
            'id' => 1
        );

        $results = array(
            'Results' => array(
                array(
                    'id' => 1
                )
            )
        );

        $update = array(
            '_OPTIONS_' => array('multiple' => true),
            array(
                'id' => 1,
                'isClosed' => 'Y',
                '_OPTIONS_' => array('force' => true)
            )
        );

        $this->expectedRestCallInOrder('Task', 'GET', array('id' => 1, 'isClosed' => 'N', 'limit' => 'all'))
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Task', 'PUT', $update);

        $this->sut->closeByQuery($query);
    }

    /**
     * @group entity_services
     */
    public function testCloseByQueryWithResults()
    {
        $query = array(
            'foo' => 'bar'
        );

        $results = array(
            'Results' => array(
                array(
                    'id' => 1
                ),
                array(
                    'id' => 2
                )
            )
        );

        $update = array(
            '_OPTIONS_' => array('multiple' => true),
            array(
                'id' => 1,
                'isClosed' => 'Y',
                '_OPTIONS_' => array('force' => true)
            ),
            array(
                'id' => 2,
                'isClosed' => 'Y',
                '_OPTIONS_' => array('force' => true)
            )
        );

        $this->expectedRestCallInOrder('Task', 'GET', array('foo' => 'bar', 'isClosed' => 'N', 'limit' => 'all'))
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Task', 'PUT', $update);

        $this->sut->closeByQuery($query);
    }
}
