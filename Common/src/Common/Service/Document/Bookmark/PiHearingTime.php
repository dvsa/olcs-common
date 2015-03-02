<?php
namespace Common\Service\Document\Bookmark;

/**
 * PiHearingDate
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PiHearingTime extends SingleValueAbstract
{
    const FORMATTER = 'Time';
    const FIELD  = 'hearingDate';
    const SERVICE = 'PiHearing';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'hearing';
}
