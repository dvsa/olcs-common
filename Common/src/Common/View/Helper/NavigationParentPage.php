<?php

namespace Common\View\Helper;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Return Parent page of active page based on hierachy (breadcrumbs)
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class NavigationParentPage extends AbstractHelper
{
    /**
     * Return a url to navigation Parent
     *
     * @return \Laminas\Navigation\Page\Mvc
     */
    public function __invoke()
    {
        /** @var \Laminas\View\Helper\Navigation\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->view->navigation('navigation')->breadcrumbs();
        $active = $breadcrumbs->findActive($breadcrumbs->getContainer());

        if (!isset($active['page'])) {
            return null;
        }

        /** @var \Laminas\Navigation\Page\Mvc $page */
        $page = $active['page'];

        return $page->getParent();
    }
}
