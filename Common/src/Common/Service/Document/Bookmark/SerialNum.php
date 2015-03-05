<?php

/**
 * SerialNum.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TodaysDate;

/**
 * Class SerialNum
 *
 * This bookmark generates a licence number in the format of:
 *
 * <code>
 *  <licence number> <current date/time>
 * </code>
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SerialNum extends LicenceNum
{
    /**
     * Return the serial number as a string in the format of "licenceNo currentDateTime"
     *
     * @return string
     */
    public function render()
    {
        $todaysDate = new TodaysDate();

        return parent::render() . ' ' . $todaysDate->render();
    }
}
