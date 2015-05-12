<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\InsMoreFreqYes;

/**
 * InsMoreFreqYes bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsMoreFreqYesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new InsMoreFreqYes();
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
     */
    public function testRenderWithInsMoreFreqYes($flag, $expected)
    {
        $bookmark = new InsMoreFreqYes();
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
            [1, 'X'],
            [0, '']
        ];
    }
}
