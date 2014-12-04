<?php

namespace Common\Service\Data;

use Common\Service\Data\Application;

/**
 * Service Class Trait
 *
 * @package Common\Service\Data
 */
trait ApplicationAwareTrait
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @param Application $Application
     */
    public function setApplicationService(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplicationService()
    {
        return $this->application;
    }
}
