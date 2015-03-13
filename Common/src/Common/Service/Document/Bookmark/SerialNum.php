<?php

/**
 * SerialNum.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Common\Service\Helper\DateHelperService;

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
class SerialNum extends LicenceNumber implements DateHelperAwareInterface
{
    private $helper;

    public function setDateHelper(DateHelperService $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Return the serial number as a string in the format of "licenceNo currentDateTime"
     *
     * @return string
     */
    public function render()
    {
        return parent::render() . ' ' . $this->helper->getDate('d/m/Y H:i:s');
    }
}
