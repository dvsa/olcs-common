<?php

namespace CommonTest\Common\Data\Object\Search;

use Common\Data\Object\Search\Licence;
use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class LicenceTest
 * @package CommonTest\Data\Object\Search
 */
class LicenceTest extends SearchAbstractTest
{
    protected $class = Licence::class;
}
