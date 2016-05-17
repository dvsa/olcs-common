<?php

namespace Common\View\Helper\Traits;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Utility class for helpers
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
trait Utils
{
    /** @var  callable */
    private $hlpEscapeHtml;

    public function escapeHtml($val)
    {
        if ($this->hlpEscapeHtml === null) {
            $view = $this->getView();
            if (method_exists($view, 'plugin')) {
                $this->hlpEscapeHtml = $view->plugin('escapehtml');
            }

            if (!$this->hlpEscapeHtml instanceof EscapeHtml) {
                $this->hlpEscapeHtml = new EscapeHtml();
            }
        }

        $fnc = $this->hlpEscapeHtml;
        return $fnc($val);
    }
}
