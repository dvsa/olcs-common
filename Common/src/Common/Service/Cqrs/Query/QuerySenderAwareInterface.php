<?php

/**
 * Query Sender Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

/**
 * Query Sender Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface QuerySenderAwareInterface
{
    /**
     * Set query sender
     *
     * @param QuerySender $querySender
     */
    public function setQuerySender(QuerySender $querySender);

    /**
     * Get query sender
     *
     * @return QuerySender
     */
    public function getQuerySender();
}
