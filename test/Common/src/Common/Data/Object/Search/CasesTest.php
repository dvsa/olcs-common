<?php

namespace CommonTest\Data\Object\Search;

use Common\Data\Object\Search\Cases;
use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class CasesTest
 * @package CommonTest\Data\Object\Search
 */
class CasesTest extends SearchAbstractTest
{
    protected $class = Cases::class;
}
