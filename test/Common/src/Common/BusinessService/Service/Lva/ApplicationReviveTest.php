<?php

/**
 * ApplicationReviveTest.php
 */
namespace CommonTest\BusinessService\Service\Lva;

use Common\Service\Entity\LicenceEntityService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use CommonTest\Bootstrap;

use Common\BusinessService\Response;
use Common\BusinessService\Service\Lva\ApplicationRevive;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Class ApplicationReviveTest
 *
 * Application revive test.
 *
 * @package CommonTest\BusinessService\Service\Lva
 */
class ApplicationReviveTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationRevive();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithoutApplication()
    {
        $expectedType = Response::TYPE_FAILED;

        $result = $this->sut->process(array());

        $this->assertInstanceOf('Common\BusinessService\Response', $result);
        $this->assertEquals($result->getType(), $expectedType);
    }

    /**
     * @dataProvider testProcessWithApplicationDataProvider
     */
    public function testProcessWithApplication($params, $applicationStatus, $licenceStatus)
    {
        $expectedType = Response::TYPE_SUCCESS;

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getLicenceIdForApplication')
                ->with($params['application']['id'])
                ->andReturn(1)
                ->shouldReceive('forceUpdate')
                ->with(
                    $params['application']['id'],
                    array(
                        'status' => $applicationStatus
                    )
                )
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('setLicenceStatus')
                ->with($params['application']['id'], $licenceStatus)
                ->getMock()
        );

        $result = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $result);
        $this->assertEquals($result->getType(), $expectedType);
    }

    // Data Providers

    public function testProcessWithApplicationDataProvider()
    {
        return array(
            'NTU Application' => array(
                array(
                    'application' => array(
                        'id' => 1,
                        'isVariation' => false,
                        'status' => array(
                            'id' => ApplicationEntityService::APPLICATION_STATUS_NOT_TAKEN_UP
                        )
                    )
                ),
                ApplicationEntityService::APPLICATION_STATUS_GRANTED,
                LicenceEntityService::LICENCE_STATUS_GRANTED
            ),
            'Withdrawn Application' => array(
                array(
                    'application' => array(
                        'id' => 1,
                        'isVariation' => false,
                        'status' => array(
                            'id' => ApplicationEntityService::APPLICATION_STATUS_WITHDRAWN
                        )
                    )
                ),
                ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION,
                LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION
            )
        );
    }
}
