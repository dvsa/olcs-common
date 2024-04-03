<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PiReportRecordFactory implements FactoryInterface
{
    /**
     * @param  $requestedName
     * @param  array|null         $options
     * @return PiReportRecord
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $urlHelper = $container->get('Helper\Url');
        return new PiReportRecord($urlHelper);
    }
}
