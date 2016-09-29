<?php

namespace Common\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

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
     * @return \Zend\Navigation\Page\Mvc
     */
    public function __invoke()
    {
        /** @var \Zend\View\Helper\Navigation\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->view->navigation('navigation')->breadcrumbs();
        $active = $breadcrumbs->findActive($breadcrumbs->getContainer());

        if (!isset($active['page'])) {
            return null;
        }

        /** @var \Zend\Navigation\Page\Mvc $page */
        $page = $active['page'];

        return $page->getParent();
    }
}
