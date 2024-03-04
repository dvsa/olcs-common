<?php

namespace Common\Service\Qa;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class DateNotInPastValidatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DateNotInPastValidator
    {
        $options ??= [];

        return new DateNotInPastValidator(
            $container->get('QaDateTimeFactory'),
            $options
        );
    }
}
