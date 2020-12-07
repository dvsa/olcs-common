<?php

namespace Common\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Common\Service\Cqrs\Query\QuerySender;
use Laminas\Mvc\MvcEvent;

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
     * @param array    $toggleConfig
     * @param MvcEvent $e
     *
     * @return bool
     */
    public function __invoke(array $toggleConfig, MvcEvent $e): bool
    {
        $action = strtolower($e->getRouteMatch()->getParam('action'));

        //check for config specific to the action
        if (isset($toggleConfig[$action])) {
            if (!empty($toggleConfig[$action])) {
                return $this->querySender->featuresEnabled($toggleConfig[$action]);
            }

            return true;
        }

        //we've nothing specific to the action, so check for a default
        if (isset($toggleConfig['default'])) {
            if (!empty($toggleConfig['default'])) {
                return $this->querySender->featuresEnabled($toggleConfig['default']);
            }

            return true;
        }

        //we don't have config set up, disable the controller by default
        return false;
    }
}
