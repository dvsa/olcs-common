<?php

namespace Common\Service\Document\Bookmark\Interfaces;

use Common\Service\Helper\DateHelperService;

/**
 * Date Helper Aware Interface
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
interface DateHelperAwareInterface
{
    public function setDateHelper(DateHelperService $helper);
}
