<?php

/**
 * AccessedCorrespondenceTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;

use Common\Service\Table\Formatter\AccessedCorrespondence;

/**
 * Class AccessedCorrespondenceTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class AccessedCorrespondenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'correspondence/access',
                        array(
                            'correspondenceId' => $data['correspondence']['id']
                        )
                    )
                    ->andReturn('LICENCE_URL')
                    ->getMock()
            );

        $this->assertEquals($expected, AccessedCorrespondence::format($data, array(), $sm->getMock()));
    }

    public function formatProvider()
    {
        return array(
            array(
                array(
                    'correspondence' => array(
                        'id' => 1,
                        'accessed' => 'N',
                        'document' => array(
                            'description' => 'Description'
                        )
                    )
                ),
                '<span class="new">&#9679;</span> <a class="strong" href="LICENCE_URL"><b>Description</b></a>'
            ),
            array(
                array(
                    'correspondence' => array(
                        'id' => 1,
                        'accessed' => 'Y',
                        'document' => array(
                            'description' => 'Description'
                        )
                    )
                ),
                '<a class="strong" href="LICENCE_URL"><b>Description</b></a>'
            )
        );
    }
}
