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
    protected function pickValidKeys($values, $keys)
    {
        return array_intersect_key($values, array_flip($keys));
    }

    protected function getVersion()
    {
        return $this->request->getQuery()->get('version', false);
    }
}
