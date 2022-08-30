<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\AbstractListDataServiceServices;
use CommonTest\Service\Data\AbstractDataServiceTestCase;

/**
 * AbstractListDataServiceTestCase
 */
class AbstractListDataServiceTestCase extends AbstractDataServiceTestCase
{
    /** @var  AbstractListDataServiceServices */
    protected $abstractListDataServiceServices;

    protected function setUp(): void
    {
        parent::setUp();

        $this->abstractListDataServiceServices = new AbstractListDataServiceServices(
            $this->abstractDataServiceServices
        );
    }
}
