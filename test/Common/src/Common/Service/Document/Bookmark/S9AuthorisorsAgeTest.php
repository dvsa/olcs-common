<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\S9AuthorisorsAge;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class S9AuthorisorsAgeTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $bookmark = new S9AuthorisorsAge();

        $this->assertEquals('', $bookmark->render());
    }
}
