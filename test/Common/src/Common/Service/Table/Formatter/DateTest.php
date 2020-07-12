<?php

/**
 * Date formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\Date;

/**
 * Date formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @todo the Date formatter now appears to rely on global constants defined
     * in the Common\Module::modulesLoaded method which can cause this test to
     * fail :(
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group DateFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $sm = $this->createMock('\stdClass', array('get'));
        $this->assertEquals($expected, Date::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array('date' => '2013-01-01'), array('dateformat' => 'd/m/Y', 'name' => 'date'), '01/01/2013'),
            array(array('date' => '2013-12-31'), array('dateformat' => 'd/m/Y', 'name' => 'date'), '31/12/2013'),
            array(array('date' => '2013-12-31'), array('dateformat' => 'Y', 'name' => 'date'), '2013'),
            array(array('date' => '2013-12-31'), array('name' => 'date'), '31/12/2013'),
            array(array('someDate' => array('date' => '2013-12-31')), array('name' => 'someDate'), '31/12/2013'),
            array(array('date' => null), array('name' => 'date'), ''),
            array(array(), array('name' => 'date'), '')
        );
    }
}
