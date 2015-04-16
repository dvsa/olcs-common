<?php

/**
 * Current User view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Current User view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CurrentUser extends AbstractHelper
{
    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return 'Terry Barret-Edgecombe';
    }
}
