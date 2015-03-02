<?php

/**
 * Licence People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Printing;

use CommonTest\Bootstrap;
use Common\Service\Lva\LicencePeopleLvaService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Licence People LVA Service tests
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleLvaServiceTest extends MockeryTestCase
{
    private $sm;
    private $sut;

    public function setup()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $this->sut = new LicencePeopleLvaService();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAddVariationMessage()
    {
        $controller = m::mock()
            ->shouldReceive('params')
            ->with('licence')
            ->andReturn(7)
            ->shouldReceive('url')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromRoute')
                ->with('lva-licence/variation', ['licence' => 7])
                ->andReturn('a-link')
                ->getMock()
            )
            ->getMock();

        $this->setService(
            'Helper\Translation',
            m::mock()
            ->shouldReceive('translateReplace')
            ->with('variation-people-message', ['a-link'])
            ->andReturn('A translated message')
            ->getMock()
        );

        $this->setService(
            'ViewHelperManager',
            m::mock()
            ->shouldReceive('get')
            ->with('placeholder')
            ->andReturn(
                m::mock()
                ->shouldReceive('getContainer')
                ->with('guidance')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('append')
                    ->with('A translated message')
                    ->getMock()
                )
                ->getMock()
            )->getMock()
        );

        $this->sut->addVariationMessage($controller);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
