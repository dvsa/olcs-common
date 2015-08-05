<?php

/**
 * PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * PersonName view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class PersonName extends AbstractHelper
{
    /**
     * Html escape helper
     *
     * @var EscapeHtml
     */
    protected $escapeHtmlHelper;

    /**
     * Get the HTML to render a persons name
     *
     * @param array $address
     *
     * @return string HTML
     */
    public function __invoke(
        array $person,
        $fields = [
            'title',
            'forename',
            'familyName'
        ]
    ) {
        $parts = array();
        $escapeHtml = $this->getEscapeHtmlHelper();

        foreach ($fields as $item) {
            if (isset($person[$item]) && !empty($person[$item])) {
                $parts[] = $escapeHtml($person[$item]);
            }
        }

        return implode(' ', $parts);
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
