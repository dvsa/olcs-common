<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\OpName;

/**
 * OpName test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class OpNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new OpName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRenderValidDataProvider()
    {
        return array(
            array(
                "Testing Test Limited\nT/A: Trading Test Limited",
                array(
                    'organisation' => array(
                        'name' => 'Testing Test Limited',
                        'tradingNames' => array(
                            array(
                                'name' => 'Trading Test Limited'
                            )
                        ),
                    )
                )
            ),
            array(
                "Testing Test Limited",
                array(
                    'organisation' => array(
                        'name' => 'Testing Test Limited',
                        'tradingNames' => array(),
                    )
                )
            )
        );
    }

    /**
     * @dataProvider testRenderValidDataProvider
     */
    public function testRender($expected, $results)
    {
        $bookmark = new OpName();
        $bookmark->setData($results);

        $this->assertEquals($expected, $bookmark->render());
    }
}
