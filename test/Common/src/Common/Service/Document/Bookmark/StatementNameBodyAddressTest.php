<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\StatementNameBodyAddress as Sut;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementNameBodyAddressTest extends \PHPUnit_Framework_TestCase
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
            'requestorsBody' => 'Some Body or Business',
            'requestorsContactDetails' => [
                'person' => [
                    'forename' => 'James',
                    'familyName' => 'Smith'
                ],
                'address' => [
                    'addressLine1' => 'A1',
                    'addressLine2' => 'A2',
                    'addressLine3' => 'A3',
                    'addressLine4' => 'A4',
                    'town' => 'A5',
                    'postcode' => 'A6'
                ]
            ]
        ];

        $bookmark->setData($data);

        $this->assertEquals('James Smith, Some Body or Business, A1, A2, A3, A4, A5, A6', $bookmark->render());
    }
}
