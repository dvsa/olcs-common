<?php

namespace Common\Controller;

use Common\Controller\Interfaces\ToggleAwareInterface;
use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Common\Controller\Plugin\FeaturesEnabled as FeaturesEnabledPlugin;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;

/**
 * Abstract controller
 *
 * In general, methods in this controller should be kept to a minimum.
 * It should only be used for controller functionality needing to be shared between the selfserve and internal repos
 *
 * @method FeaturesEnabledPlugin featuresEnabled(array $toggleConfig, MvcEvent $e)
 * @method Response handleQuery(QueryInterface $query)
 * @method Response handleCommand(CommandInterface $query)
 * @method \Common\Controller\Plugin\Redirect redirect()
 *
 * @todo this would also now be redundant
 */
abstract class AbstractOlcsController extends AbstractActionController
{
    /**
     * @todo this attribute would not be needed
     *
     * @var array
     *
     * Config for feature toggles - for usage see https://wiki.i-env.net/display/olcs/Feature+toggles
     */
    protected $toggleConfig = [];

    public function onDispatch(MvcEvent $e)
    {
        // @todo Can we extract this to a mvc listener class?
        if ($this instanceof ToggleAwareInterface && !$this->featuresEnabled($this->toggleConfig, $e)) {
            return $this->notFoundAction();
        }

        return parent::onDispatch($e);
    }
}
