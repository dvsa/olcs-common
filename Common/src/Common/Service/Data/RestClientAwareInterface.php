<?php

namespace Common\Service\Data;

use Common\Util\RestClient;

/**
 * Interface RestClientAwareInterface
 * @package Common\Service\Data
 */
interface RestClientAwareInterface
{
    public function setRestClient(RestClient $restClient);
    public function getRestClient();
    public function getServiceName();
}
