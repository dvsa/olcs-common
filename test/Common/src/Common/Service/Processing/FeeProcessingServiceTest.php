<?php

/**
 * Fee Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Processing\FeeProcessingService;
use Common\Service\Data\FeeTypeDataService;

/**
 * Fee Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FeeProcessingServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new FeeProcessingService();
        $this->sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGenerateDocumentWithGrantFee()
    {
        $mockFile = m::mock();

        $params = [
            'fee' => 421,
            'application' => 44,
            'licence' => 100
        ];

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateAndStore')
            ->with('FEE_REQ_GRANT_GV', 'Goods Grant Fee Request', $params)
            ->andReturn($mockFile)
            ->getMock()
        );

        $this->setService(
            'Helper\DocumentDispatch',
            m::mock()
            ->shouldReceive('process')
            ->with(
                $mockFile,
                [
                    'description' => 'Goods Grant Fee Request',
                    'filename'    => 'Goods_Grant_Fee_Request.rtf',
                    'application' => $params['application'],
                    'licence'     => $params['licence'],
                    'category'    => 1,
                    'subCategory' => 110,
                    'isExternal'  => false
                ]
            )
            ->getMock()
        );

        $this->sut->generateDocument(FeeTypeDataService::FEE_TYPE_GRANT, $params);
    }

    public function testGenerateDocumentWithNonGrantFee()
    {
        $params = [];

        $this->setService(
            'Helper\DocumentGeneration',
            m::mock()
            ->shouldReceive('generateAndStore')
            ->never()
            ->getMock()
        );

        $this->setService(
            'Helper\DocumentDispatch',
            m::mock()
            ->shouldReceive('process')
            ->never()
            ->getMock()
        );

        $this->sut->generateDocument('foo', $params);
    }

    private function setService($service, $mock)
    {
        $this->sm->shouldReceive('get')
            ->with($service)
            ->andReturn($mock);
    }
}
