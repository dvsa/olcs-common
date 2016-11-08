<?php

namespace Common\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Create a link '< Back'
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class LinkBack extends AbstractHelper implements FactoryInterface
{
    /** @var  \Zend\Http\PhpEnvironment\Request */
    private $request;

    /**
     * Factory
     *
     * @param \Zend\View\HelperPluginManager $sl Service Manager
     *
     * @return $this;
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $this->request = $sl->getServiceLocator()->get('Request');

        return $this;
    }

    /**
     * Return a back link
     *
     * @param array|null $params Parameters
     *
     * @return string
     */
    public function __invoke(array $params = null)
    {
        $label = (isset($params['label']) ? $params['label'] : null);
        $isNeedEscape = (!isset($params['escape']) || $params['escape'] !== false);
        $url = (!empty($params['url']) ? $params['url'] : null);

        if (null === $label) {
            $label = 'common.link.back.label';
        }

        if (null === $url) {
            /** @var \Zend\Http\Header\Referer $header */
            $header = $this->request->getHeader('referer');

            if ($header === false) {
                return '';
            }

            $url = $header->uri()->getPath();
        }

        $label = $this->view->translate($label);

        return
            '<a href="' . $url . '" class="back-link">' .
                ($isNeedEscape ? $this->view->escapeHtml($label) : $label) .
            '</a>';
    }
}
