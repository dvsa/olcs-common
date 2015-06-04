<?php

/**
 * Test LicenceStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use Common\View\Helper\LicenceStatus;
use Common\Service\Entity\LicenceEntityService;

/**
 * Test LicenceStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceStatusTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    private $mockView;
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->sut = new LicenceStatus();

        $this->mockView = \Mockery::mock('Zend\View\Renderer\PhpRenderer');
        $this->sut->setView($this->mockView);
    }

    /**
     * @dataProvider dataProviderLicenceStatus
     */
    public function testLicenceStatus($status, $color)
    {
        $this->mockView->shouldReceive('translate')->with($status)->once()->andReturn('TRANSLATED');

        $html = $this->sut->__invoke($status);

        $this->assertEquals('<span class="status '. $color .'">TRANSLATED</span>', $html);
    }

    public function dataProviderLicenceStatus()
    {
        return [
            [LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION, 'orange'],
            [LicenceEntityService::LICENCE_STATUS_NOT_SUBMITTED, 'grey'],
            [LicenceEntityService::LICENCE_STATUS_SUSPENDED, 'orange'],
            [LicenceEntityService::LICENCE_STATUS_VALID, 'green'],
            [LicenceEntityService::LICENCE_STATUS_CURTAILED, 'orange'],
            [LicenceEntityService::LICENCE_STATUS_GRANTED, 'orange'],
            [LicenceEntityService::LICENCE_STATUS_SURRENDERED, 'red'],
            [LicenceEntityService::LICENCE_STATUS_WITHDRAWN, 'red'],
            [LicenceEntityService::LICENCE_STATUS_REFUSED, 'red'],
            [LicenceEntityService::LICENCE_STATUS_REVOKED, 'red'],
            [LicenceEntityService::LICENCE_STATUS_NOT_TAKEN_UP, 'red'],
            [LicenceEntityService::LICENCE_STATUS_TERMINATED, 'red'],
            [LicenceEntityService::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT, 'red'],
        ];
    }
}
