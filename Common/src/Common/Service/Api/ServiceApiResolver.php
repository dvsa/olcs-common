<?php

/**
 * ServiceApiResolver
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
namespace Common\Service\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Util\ResolveApi;

/**
 * ServiceApiResolver
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
class ServiceApiResolver implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $serviceApiMapping['endpoints'] = $config['service_api_mapping']['endpoints'];
        foreach ($config['service_api_mapping']['endpoints'] as $key => $endpoint) {
            if (isset($config['service_api_mapping']['apis'][$key])) {
                foreach ($config['service_api_mapping']['apis'][$key] as $api => $path) {
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
