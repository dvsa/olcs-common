<?php

/**
 * GracePeriodEntityServiceTest.php
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\GracePeriodEntityService;

/**
 * Class GracePeriodEntityServiceTest
 *
 * GracePeriodEntityService test.
 *
 * @package CommonTest\Service\Entity
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriodEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new GracePeriodEntityService();

        parent::setUp();
    }

    public function testGetGracePeriodsForLicence()
    {
        $licenceId = 7;

        $this->expectOneRestCall(
            'GracePeriod',
            'GET',
            array(
                'licence' => $licenceId,
                'sort' => 'startDate',
                'order' => 'ASC'
            )
        );

        $this->sut->getGracePeriodsForLicence($licenceId);
    }
}
