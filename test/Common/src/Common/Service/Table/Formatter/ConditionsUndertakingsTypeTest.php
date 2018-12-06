<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;

use Common\Service\Table\Formatter\ConditionsUndertakingsType;

/**
 * Class ConditionsUndertakingsTypeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ConditionsUndertakingsTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatNoS4()
    {
        $data = [
            'conditionType' => [
                'description' => 'DESCRIPTION'
            ],
            's4' => null
        ];
        $column = null;
        $sm = null;

        $this->assertSame('DESCRIPTION', ConditionsUndertakingsType::format($data, $column, $sm));
    }

    public function testFormatWithS4()
    {
        $data = [
            'conditionType' => [
                'description' => 'DESCRIPTION'
            ],
            's4' => ['FOO']
        ];
        $column = null;
        $sm = m::mock();
        $sm->shouldReceive('get->translate')->with('(Schedule 4/1)')->once()->andReturn('TRANSLATED');

        $this->assertSame('DESCRIPTION<br>TRANSLATED', ConditionsUndertakingsType::format($data, $column, $sm));
    }
}
