<?php
namespace Common\Service\Cqrs\Query;

use Common\Service\Cqrs\Response;
use Dvsa\Olcs\Transfer\Query\QueryContainerInterface;

/**
 * Query
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface QueryServiceInterface
{
    /**
     * Send a query and return the response
     *
     * @param QueryContainerInterface $query
     * @return Response
     */
    public function send(QueryContainerInterface $query);
}
