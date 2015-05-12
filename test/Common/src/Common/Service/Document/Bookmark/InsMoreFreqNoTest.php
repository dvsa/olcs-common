<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\InsMoreFreqNo;

/**
 * InsMoreFreqNo bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsMoreFreqNoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new InsMoreFreqNo();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    /**
     * @dataProvider safetyInsProvider
     * @group InsMoreFreqNoTest
     */
    public function testRenderWithInsMoreFreqNo($flag, $expected)
    {
        $bookmark = new InsMoreFreqNo();
        $bookmark->setData(
            [
                'safetyInsVaries' => $flag
            ]
        );

        $this->assertEquals(
            $expected,
            $bookmark->render()
        );
    }
    
    public function safetyInsProvider()
    {
        return [
            [0, 'X'],
            [1, '']
        ];
    }
}
