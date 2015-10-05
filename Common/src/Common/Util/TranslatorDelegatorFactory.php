<?php

/**
 * Translator Delegator Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Util;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Translator Delegator Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslatorDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $realTranslator = $callback();

        $config = $serviceLocator->get('Config');

        $replacements = isset($config['translator']['replacements']) ? $config['translator']['replacements'] : '';

        return new TranslatorDelegator($realTranslator, $replacements);
    }
}
