<?php

namespace Common\Service\Document\Bookmark\Interfaces;

use Common\Service\Helper\DateHelperService;

interface DateHelperAwareInterface
{
    public function setDateHelper(DateHelperService $helper);
}
