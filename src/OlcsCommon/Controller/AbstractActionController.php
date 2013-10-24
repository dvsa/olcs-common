<?php
/**
 * An abstract controller that all ordinary OLCS controllers inherit from
 *
 * @package     olcscommon
 * @subpackage  controller
 * @author      Pelle Wessman <pelle.wessman@valtech.se>
 */

namespace OlcsCommon\Controller;

use \Olcs\Utility\RestClient;

class AbstractActionController extends \Zend\Mvc\Controller\AbstractRestfulController
{
    /**
     * Creates and returns a client for a specific service API
     *
     * @param string $service The name of the service to return a client for
     * @return RestClient A client configured for the desired service
     */
    protected function service($service)
    {
        $serviceApiResolver = $this->getServiceLocator()->get('ServiceApiResolver');
        return $serviceApiResolver->getClient($service);
    }
}
