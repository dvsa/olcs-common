<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\TranslateableTextParameterHandler;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;
use Zend\View\Helper\AbstractHelper;

/**
 * TranslateableTextParameterHandlerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TranslateableTextParameterHandlerTest extends MockeryTestCase
{
    private $helper;

    private $helperName;

    private $sut;

    public function setUp()
    {
        $this->sut = new TranslateableTextParameterHandler();

        $this->helper = m::mock(AbstractHelper::class);
        $this->helperName = 'currency';

        $this->sut->registerFormatter($this->helperName, $this->helper);
    }

    public function testWithNoFormatter()
    {
        $parameter = ['value' => '87'];

        $this->assertEquals(
            '87',
            $this->sut->handle($parameter)
        );
    }

    public function testWithFormatter()
    {
        $unformattedValue = '42.00';
        $formattedValue = '42';

        $this->helper->shouldReceive('__invoke')
            ->with($unformattedValue)
            ->andReturn($formattedValue);

        $parameter = [
            'value' => $unformattedValue,
            'formatter' => $this->helperName
        ];

        $this->assertEquals(
            $formattedValue,
            $this->sut->handle($parameter)
        );
    }

    public function testExceptionWithUnknownFormatter()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown formatter permitStatus');

        $parameter = [
            'value' => 'not_valid',
            'formatter' => 'permitStatus'
        ];

        $this->sut->handle($parameter);
    }
}
