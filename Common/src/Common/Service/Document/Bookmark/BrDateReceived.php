<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrDateReceived extends BusRegFlatAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'receivedDate';
    const SERVICE = 'BusReg';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
}
