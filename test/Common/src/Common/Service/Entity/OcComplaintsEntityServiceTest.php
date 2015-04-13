<?php

namespace CommonTest\Service\Entity;

use Common\Service\Entity\OcComplaintsEntityService;

/**
 * Class OcComplaintsEntityServiceTest
 *
 * Entity service test.
 *
 * @package CommonTest\Service\Entity
 */
class OcComplaintsEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected $sut = null;

    protected function setUp()
    {
        $this->sut = new OcComplaintsEntityService();

        parent::setUp();
    }

    public function testGetCountComplaintsForOpCentre()
    {
        $this->expectOneRestCall(
            'OcComplaint',
            'GET',
            [
                'operatingCentre' => 1,
            ]
        )->will(
            $this->returnValue(
                [
                    'Count' => 1
                ]
            )
        );

        $this->assertEquals(1, $this->sut->getCountComplaintsForOpCentre(1));
    }
}
