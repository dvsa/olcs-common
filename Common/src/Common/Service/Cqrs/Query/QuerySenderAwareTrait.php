<?php

/**
 * Query Sender Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Cqrs\Query;

/**
 * Query Sender Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait QuerySenderAwareTrait
{
    /**
     * @var QuerySender
     */
    protected $querySender;

    /**
     * Set query sender
     *
     * @param QuerySender $querySender
     */
    public function setQuerySender(QuerySender $querySender)
    {
        $this->querySender = $querySender;
    }

    /**
     * Get query sender
     *
     * @return QuerySender
     */
    public function getQuerySender()
    {
        return $this->querySender;
    }
}
