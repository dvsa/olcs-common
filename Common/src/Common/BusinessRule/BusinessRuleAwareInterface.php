<?php

/**
 * Business Rule Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule;

/**
 * Business Rule Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface BusinessRuleAwareInterface
{
    public function setBusinessRuleManager(BusinessRuleManager $businessRuleManager);

    public function getBusinessRuleManager();
}
