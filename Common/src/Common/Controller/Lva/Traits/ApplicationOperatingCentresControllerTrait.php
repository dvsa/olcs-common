<?php

namespace Common\Controller\Lva\Traits;

/**
 * Application Operating Centres Controller Trait
 */
trait ApplicationOperatingCentresControllerTrait
{
    protected function getDocumentProperties()
    {
        return array(
            'application' => $this->getIdentifier(),
            'licence' => $this->getLicenceId()
        );
    }

    // @TODO port setTrafficAreaAfterCrud
}
