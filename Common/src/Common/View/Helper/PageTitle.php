<?php

namespace Common\View\Helper;

use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\AbstractHelper;
use Laminas\View\Helper\Placeholder;
use Olcs\Logging\Log\Logger;

/**
 * Page Title
 */
class PageTitle extends AbstractHelper
{
    private Translate $translator;

    private Placeholder $placeholder;

    private $routeMatchName;

    private $action;

    public function __construct(Translate $translator, Placeholder $placeholder, $routeMatchName = null, $action = null)
    {
        $this->translator = $translator;
        $this->placeholder = $placeholder;
        $this->routeMatchName = $routeMatchName;
        $this->action = $action;
    }

    /**
     * Return a page title for the current page
     *
     * @return string
     */
    public function __invoke()
    {
        // get pageTitle placeholder value
        $pageTitle = (string)$this->placeholder->getContainer('pageTitle');

        if (($pageTitle === '' || $pageTitle === '0') && !empty($this->routeMatchName)) {
            // try page title based on routing
            $pageTitleRouteKey = implode('.', array_filter(['page.title', $this->routeMatchName, $this->action]));

            if ($pageTitleRouteKey !== $this->translator->__invoke($pageTitleRouteKey)) {
                // translated value exists - use it
                $pageTitle = $pageTitleRouteKey;
            } else {
                // Log the fact that we are missing a page title
                Logger::info('Missing page title...', ['data' => ['key' => $pageTitleRouteKey]]);
            }
        }

        return $this->translator->__invoke($pageTitle);
    }
}
