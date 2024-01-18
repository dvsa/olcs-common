<?php

declare(strict_types=1);

namespace Common\FormService\Form\Continuation;

use Common\Service\Helper\FormHelperService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConditionsUndertakingsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ConditionsUndertakings
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConditionsUndertakings
    {
        $formHelper = $container->get(FormHelperService::class);
        return new ConditionsUndertakings($formHelper);
    }
}
