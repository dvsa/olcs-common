<?php

namespace CommonTest\View\Helper;

use Common\Service\Cqrs\Query\CachingQueryService as QueryService;
use Common\View\Helper\SystemInfoMessages;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;
use Dvsa\Olcs\Transfer\Query\System\InfoMessage\GetListActive;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Stdlib\ResponseInterface;

/**
 * @covers Common\View\Helper\SystemInfoMessages
 */
class SystemInfoMessagesTest extends MockeryTestCase
{
    /** @var m\MockInterface|QueryService */
    protected $mockQuerySrv;
    /** @var m\MockInterface|AnnotationBuilder $mockAnnotationBuilder */
    private $mockAnnotationBuilder;

    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->mockAnnotationBuilder = m::mock(AnnotationBuilder::class);
        $this->mockQuerySrv = m::mock(QueryService::class);
    }

    /**
     * @dataProvider dataProviderTest
     */
    public function test($data, $expect)
    {
        $isOk = ($data !== null);

        $mockQryCntr = m::mock(QueryContainerInterface::class);

        $this->mockAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(GetListActive::class))
            ->once()
            ->andReturn($mockQryCntr);

        $mockResp = m::mock(ResponseInterface::class);
        $mockResp->shouldReceive('getResult')
            ->withNoArgs()
            ->times((int)$isOk)
            ->andReturn($data)
            //
            ->shouldReceive('isOk')
            ->withNoArgs()
            ->andReturn($isOk);

        $this->mockQuerySrv->shouldReceive('send')
            ->once()
            ->with($mockQryCntr)
            ->andReturn($mockResp);

        $invoke = new SystemInfoMessages($this->mockAnnotationBuilder, $this->mockQuerySrv);
        $actual = $invoke(true); // __invoke

        static::assertEquals($expect, $actual);
    }

    public function dataProviderTest()
    {
        return [
            //  no data
            [
                'data' => null,
                'expect' => null,
            ],
            //  has data
            [
                'data' => [
                    'count' => 2,
                    'results' => [
                        [
                            'description' => 'unit_Desc1',
                        ],
                        [
                            'description' => 'unit_Desc2 &amp;',
                        ],
                    ],
                ],
                'expect' =>
                    '<div class="system-messages">' .
                    '<div class="system-messages__wrapper"><p>unit_Desc1</p></div>' .
                    '<div class="system-messages__wrapper"><p>unit_Desc2 &amp;amp;</p></div>' .
                    '</div>',
            ],
            // no data alt
            [
                'data' => [],
                'expect' => null,
            ],
        ];
    }
}
