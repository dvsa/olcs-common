<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\StatementContactType as Sut;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementContactTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $id = '123';

        $bookmark = new Sut();

        $query = $bookmark->getQuery(['statement' => $id]);

        $this->assertEquals('Statement', $query['service']);

        $this->assertEquals(['id' => $id], $query['data']);
    }

    public function testRender()
    {
        $bookmark = new Sut();

        $data = [
            'id' => '123',
            'contactType' => [
                'description' => 'Value 1'
            ]
        ];

        $bookmark->setData($data);

        $this->assertEquals('Value 1', $bookmark->render());
    }
}
