<?php
namespace Common\Service\Document\Bookmark;

/**
 * Transport manager id bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmId extends SingleValueAbstract
{
    const SERVICE = 'TransportManager';
    const FIELD = 'id';
    const SRCH_VAL_KEY = 'transportManager';
}
