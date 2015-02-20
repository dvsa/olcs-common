<?php
/**
 * Statement Flat Abstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Bookmark\Formatter;

/**
 * Statement Flat Abstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class StatementFlatAbstract extends SingleValueAbstract
{
    const CLASS_NAMESPACE = __NAMESPACE__;
    const FORMATTER = null;
    const FIELD = null;
    const SERVICE = 'Statement';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'statement';
}
