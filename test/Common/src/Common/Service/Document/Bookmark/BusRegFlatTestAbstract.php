<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Formatter\FormatterInterface;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class BusRegFlatTestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * Implement this in the child class
     */
    const SUT_CLASS_NAME = '\Common\Service\Document\Bookmark\BOOKMARK_CLASS_NAME';

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

        $key = $sutClassName::BR_FIELD;
        $value = get_class($bookmark);

        $bookmark->setData([$key => $value]);

        $formatter = $this->getFormatter();
        if ($formatter instanceof FormatterInterface) {

            $formatterName = get_class($formatter);

            $this->assertEquals(
                $formatterName::format((array)$value),
                $bookmark->render()
            );

        } else {
            $this->assertEquals($value, $bookmark->render());
        }
    }

    public function getFormatter()
    {
        $sutClassName = static::SUT_CLASS_NAME;

        if (is_null($sutClassName::FORMATTER)) {
            return false;
        }

        $formatterClassName = $sutClassName::CLASS_NAMESPACE . '\Formatter\\' . $sutClassName::FORMATTER;

        return new $formatterClassName();
    }
}
