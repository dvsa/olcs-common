<?php
/**
 * An abstract controller that all OLCS restful controllers inherit from
 *
 * @package     olcscommon
 * @subpackage  controller
 * @author      Pelle Wessman <pelle.wessman@valtech.se>
 */

namespace OlcsCommon\Controller;

class AbstractRestfulController extends \Zend\Mvc\Controller\AbstractRestfulController
{
    const ERROR_METHOD_NOT_ALLOWED = 101;
    const ERROR_FORBIDDEN = 102;
    const ERROR_MISSING_PARAMETERS = 103;
    const ERROR_INVALID_PARAMETER = 104;
    const ERROR_UNKNOWN = 105;

    protected function pickValidKeys($values, $keys)
    {
        return array_intersect_key($values, array_flip($keys));
    }

    protected function getVersion()
    {
        return $this->request->getQuery()->get('version', false);
    }
}
