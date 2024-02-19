<?php

/**
 * Link type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Type;

use Common\Service\Helper\StackHelperService;
use Common\Service\Table\Formatter\StackValue;
use Common\Service\Table\Formatter\StackValueReplacer;

/**
 * Link type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Link extends AbstractType
{
    /**
     * Render the selector
     *
     * @param array $data
     * @param array $column
     * @return string
     */
    public function render($data, $column, $formattedContent = null)
    {
        $params = $column['params'] ?? [];

        // Not ideal, but we don't have it injected at the moment
        $sm = $this->getTable()->getServiceLocator();

        foreach ($params as $key => $param) {
            $params[$key] = (new StackValueReplacer(new StackValue(new StackHelperService())))->format($data, ['stringFormat' => $param]);
        }

        $url = $sm->get('Helper\Url')->fromRoute($column['route'], $params);

        return str_replace('[LINK]', $url, $formattedContent);
    }
}
