<?php

namespace Common\Service\Document\Bookmark;

/**
 * Licence Vehicle Limit Bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceVehicleLimit extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = null;
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'totAuthVehicles';
}
