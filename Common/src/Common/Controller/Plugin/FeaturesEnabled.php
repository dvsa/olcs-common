<?php

namespace Common\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Common\Service\Cqrs\Query\QuerySender;
use Zend\Mvc\MvcEvent;

/**
 * Class FeaturesEnabled
 * @package Common\Controller\Plugin
 */
class FeaturesEnabled extends AbstractPlugin
{
    /**
     * @var QuerySender
     */
    private $querySender;

    /**
     * @param QuerySender $sender
     */
    public function __construct(QuerySender $sender)
    {
        $this->querySender = $sender;
    }

    /**
     * @param array    $features
     * @param MvcEvent $e
     *
     * @return bool
     */
    public function __invoke(array $toggleConfig, MvcEvent $e): bool
    {
        if (empty($toggleConfig)) {
            return true;
        }

        $action = strtolower($e->getRouteMatch()->getParam('action'));

        if (isset($toggleConfig[$action])) {
            if (!empty($toggleConfig[$action])) {
                return $this->querySender->featuresEnabled($toggleConfig[$action]);
            }

            return true;
        }

        if (isset($toggleConfig['default'])) {
            if (!empty($toggleConfig['default'])) {
                return $this->querySender->featuresEnabled($toggleConfig['default']);
            }

            return true;
        }

        return true;
    }
}
