<?php

namespace Common\Service\Document\Bookmark;

/**
 * InsNoVhls bookmark - number of weeks between vehicles inspections
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsNoVhls extends SingleValueAbstract
{
    const SERVICE = 'Licence';
    const FORMATTER = null;
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'safetyInsVehicles';
}
