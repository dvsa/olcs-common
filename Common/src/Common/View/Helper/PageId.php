<?php

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * Page Id
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PageId extends AbstractHelper
{
    private $routeMatchName;

    private $action;

    public function __construct(string $routeMatchName, string $action)
    {
        $this->routeMatchName = $routeMatchName;
        $this->action = $action;
    }

    /**
     * Return a page id for the current page, which can be used in the automated tests
     *
     * @return string
     */
    public function __invoke()
    {
        return sprintf('pg:%s:%s', $this->routeMatchName, $this->action);
    }
}
