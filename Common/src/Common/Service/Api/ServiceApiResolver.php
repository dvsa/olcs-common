<?php

namespace Common\Service\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Util\ResolveApi;

/**
 * Description of ServiceApiResolver
 *
 * @author Michael Cooper
 */
class ServiceApiResolver implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        foreach ($config['service_api_mapping']['endpoints'] as $key => $endpoint) {
            if (isset($config['service_api_mapping']['apis'][$key])) {
                foreach($config['service_api_mapping']['apis'][$key] as $api => $path) {
                    $serviceApiMapping[$api] = array(
                        'baseUrl' => $endpoint,
                        'path' => $path,
                    );
                }
            }
        }
        return new ResolveApi($serviceApiMapping);
    }
}
