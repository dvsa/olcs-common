<?php

/**
 * CorrespondenceInboxEntityServiceTest.php
 */
namespace CommonTest\Service\Entity;

use Mockery as m;

use Common\Service\Entity\CorrespondenceInboxEntityService;

/**
 * Class CorrespondenceInboxEntityServiceTest
 *
 * @package CommonTest\Service\Entity
 */
class CorrespondenceInboxEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CorrespondenceInboxEntityService();

        parent::setUp();
    }

    public function testGetById()
    {
        $this->expectOneRestCall(
            'CorrespondenceInbox',
            'GET',
            1
        )->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById(1));
    }
}
