<?php

/**
 * SerialNumber.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TodaysDate;

/**
 * Class SerialNumber
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
class SerialNumber extends LicenceNumber
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
