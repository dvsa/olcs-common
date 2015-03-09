<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Licence - Undertakings
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Undertakings extends AbstractConditionsUndertakings
{
    const CONDITION_TYPE = ConditionUndertakingEntityService::TYPE_UNDERTAKING;
}
