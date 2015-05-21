<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\RegisteredNumber;

/**
 * Registered Number test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RegisteredNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new RegisteredNumber();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoRegisteredNumber()
    {
        $bookmark = new RegisteredNumber();
        $bookmark->setData(
            [
                'organisation' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithRegisteredNumber()
    {
        $bookmark = new RegisteredNumber();
        $bookmark->setData(
            [
                'organisation' => [
                    'companyOrLlpNo' => 'regNumber'
                ]
            ]
        );

        $this->assertEquals(
            'regNumber',
            $bookmark->render()
        );
    }
}
