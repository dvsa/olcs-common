<?php

namespace Common\View\Helper;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Create a link '< Back'
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class LinkBack extends AbstractHelper implements FactoryInterface
{
    /** @var  \Laminas\Http\PhpEnvironment\Request */
    private $request;

    /**
     * Factory
     *
     * @param \Laminas\View\HelperPluginManager $sl Service Manager
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
        if (empty($params['url'])) {
            /** @var \Laminas\Http\Header\Referer $header */
            $header = $this->request->getHeader('referer');

            if ($header === false) {
                return '';
            }

            $url = $header->uri()->getPath();
        } else {
            $url = $params['url'];
        }

        $label = (isset($params['label']) ? $params['label'] : 'common.link.back.label');
        $isNeedEscape = (!isset($params['escape']) || $params['escape'] !== false);

        $label = $this->view->translate($label);

        return
            '<a href="' . $url . '" class="govuk-back-link">' .
                ($isNeedEscape ? htmlspecialchars($label) : $label) .
            '</a>';
    }
}
