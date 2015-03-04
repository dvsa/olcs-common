<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\LicenceType;

/**
 * Licence holder name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new LicenceType();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);
        $this->assertEquals(['id' => 123], $query['data']);
    }

    public function testRender()
    {
        $bookmark = new LicenceType();
        $bookmark->setData(array(
            'goodsOrPsv' => array(
                'description' => 'foo'
            ),
            'licenceType' => array(
                'description' => 'bar'
            )
        ));

        $this->assertEquals('foo bar', $bookmark->render());
    }
}