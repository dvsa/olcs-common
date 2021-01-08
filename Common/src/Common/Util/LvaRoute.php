<?php

/**
 * Lva Route
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Laminas\Mvc\Router\Http\Segment;

/**
 * Lva Route
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LvaRoute extends Segment
{
    public function assemble(array $params = array(), array $options = array())
    {
        if (isset($params['action']) && $params['action'] === 'index') {
            $params['action'] = null;
        }

        return parent::assemble($params, $options);
    }
}
