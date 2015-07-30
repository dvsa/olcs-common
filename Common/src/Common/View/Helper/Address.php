<?php

/**
 * Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Address extends AbstractHelper
{

    /**
     * Html escape helper
     *
     * @var EscapeHtml
     */
    protected $escapeHtmlHelper;

    /**
     * Get the HTML to render an address array
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke(
        array $address,
        $fields = [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'town',
            'postcode',
            'countryCode'
        ]
    ) {
        $parts = array();
        $escapeHtml = $this->getEscapeHtmlHelper();

        if (isset($address['countryCode']['id'])) {
            $address['countryCode'] = $address['countryCode']['id'];
        } else {
            $address['countryCode'] = null;
        }

        foreach ($fields as $item) {
            if (isset($address[$item]) && !empty($address[$item])) {
                $parts[] = $escapeHtml($address[$item]);
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Retrieve the escapeHtml helper
     *
     * @return EscapeHtml
     */
    private function getEscapeHtmlHelper()
    {
        if ($this->escapeHtmlHelper) {
            return $this->escapeHtmlHelper;
        }

        if (method_exists($this->getView(), 'plugin')) {
            $this->escapeHtmlHelper = $this->view->plugin('escapehtml');
        }

        if (!$this->escapeHtmlHelper instanceof EscapeHtml) {
            $this->escapeHtmlHelper = new EscapeHtml();
        }

        return $this->escapeHtmlHelper;
    }
}
