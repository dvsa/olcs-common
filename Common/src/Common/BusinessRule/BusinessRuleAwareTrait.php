<?php

/**
 * Business Rule Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule;

/**
 * Business Rule Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait BusinessRuleAwareTrait
{
    protected $businessRuleManager;

    public function setBusinessRuleManager(BusinessRuleManager $businessRuleManager)
    {
        $this->businessRuleManager = $businessRuleManager;
    }

    public function getBusinessRuleManager()
    {
        return $this->businessRuleManager;
    }
}
