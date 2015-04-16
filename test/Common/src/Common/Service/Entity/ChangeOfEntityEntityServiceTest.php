<?php

namespace CommonTest\Service\Entity;

use Common\Service\Entity\ChangeOfEntityEntityService;
use Mockery as m;

/**
 * Class ChangeOfEntityEntityService
 *
 * Change of entity service test.
 *
 * @package CommonTest\Service\Entity
 */
class ChangeOfEntityEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ChangeOfEntityEntityService();

        parent::setUp();
    }

    public function testGetForLicence()
    {
        $this->expectOneRestCall('ChangeOfEntity', 'GET', array('licence' => 1))
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getForLicence(1));
    }
}
