<?php

/**
 * Guidance Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Laminas\View\Helper\Placeholder;

/**
 * Guidance Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GuidanceHelperService
{
    /** @var Placeholder */
    private $placeholder;

    public function __construct(
        Placeholder $placeholder
    ) {
        $this->placeholder = $placeholder;
    }

    public function append($message)
    {
        $this->placeholder->getContainer('guidance')->append($message);
    }
}
