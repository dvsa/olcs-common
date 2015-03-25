<?php

namespace Common\Service\Document\Bookmark;

/**
 * Interim Licence - Valid Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimValidDate extends SingleValueAbstract
{
    const SERVICE = 'Application';
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'application';
    const FIELD = 'interimStart';
}
