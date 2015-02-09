<?php

/**
 * Condition Undertaking Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Condition Undertaking Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConditionUndertakingEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ConditionUndertakingEntityService();

        parent::setUp();
    }

    public function testGetCondition()
    {
        $id = 3;

        $this->expectOneRestCall('ConditionUndertaking', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getCondition($id));
    }

    public function testGetForApplication()
    {
        $id = 3;
        $reponse = ['Results' => 'RESPONSE'];

        $this->expectOneRestCall('ConditionUndertaking', 'GET', ['application' => $id, 'limit' => 'all'])
            ->will($this->returnValue($reponse));

        $this->assertEquals('RESPONSE', $this->sut->getForApplication($id));
    }

    public function testGetForLicence()
    {
        $id = 3;
        $reponse = ['Results' => 'RESPONSE'];

        $this->expectOneRestCall('ConditionUndertaking', 'GET', ['licence' => $id, 'limit' => 'all'])
            ->will($this->returnValue($reponse));

        $this->assertEquals('RESPONSE', $this->sut->getForLicence($id));
    }
}
