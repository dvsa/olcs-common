<?php

namespace CommonTest\Service\Document\Bookmark;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrCouncilsNotifiedTest extends \PHPUnit_Framework_TestCase
{
    const SUT_CLASS_NAME = '\Common\Service\Document\Bookmark\BrCouncilsNotified';

    public function testGetQueryContainsExpectedKeys()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $busRegId = '123';

        $bookmark = new $sutClassName();

        $query = $bookmark->getQuery(['busRegId' => $busRegId]);

        $this->assertEquals('BusReg', $query['service']);

        $this->assertEquals(['id' => $busRegId], $query['data']);
    }

    public function testRender()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        $bookmark = new $sutClassName();

        $data = [
            'id' => '123',
            'localAuthoritys' => [
                0 => ['description' => 'LA 1'],
                1 => ['description' => 'L A2'],
            ]
        ];

        $bookmark->setData($data);

        $this->assertEquals('LA 1, L A2', $bookmark->render());
    }
}
