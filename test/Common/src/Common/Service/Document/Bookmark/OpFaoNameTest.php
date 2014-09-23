<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\OpFaoName;

class OpFaoNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new OpFaoName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoCorrespondenceAddress()
    {
        $bookmark = new OpFaoName();
        $bookmark->setData(
            [
                'organisation' => [
                    'contactDetails' => [
                        [
                            'contactType' => [
                                'id' => 'foo'
                            ],
                            'fao' => 'Team Leader'
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            null,
            $bookmark->render()
        );
    }

    public function testRenderWithCorrespondenceAddress()
    {
        $bookmark = new OpFaoName();
        $bookmark->setData(
            [
                'organisation' => [
                    'contactDetails' => [
                        [
                            'contactType' => [
                                'id' => 'ct_corr'
                            ],
                            'fao' => 'Team Leader'
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'Team Leader',
            $bookmark->render()
        );
    }
}
