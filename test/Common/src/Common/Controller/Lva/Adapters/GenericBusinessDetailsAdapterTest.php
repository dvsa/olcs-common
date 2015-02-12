<?php

/**
 * Generic Business Details Adapter tests
 *
 * Sure, testing a bunch of no-ops looks a bit odd, but it's handy because:
 * 1) It stops the lines showing as uncovered
 * 2) It means that if/when the default behaviour *does* change, these tests will catch it
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\GenericBusinessDetailsAdapter;

/**
 * Generic Business Details Adapter tests
 *
 * Sure, testing a bunch of no-ops looks a bit odd, but it's handy because:
 * 1) It stops the lines showing as uncovered
 * 2) It means that if/when the default behaviour *does* change, these tests will catch it
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class GenericBusinessDetailsAdapterTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new GenericBusinessDetailsAdapter();
    }

    public function testAllMethodsAreNoOps()
    {
        $this->assertNull($this->sut->alterFormForOrganisation(m::mock('Zend\Form\Form'), 123));
        $this->assertNull($this->sut->hasChangedTradingNames(123, []));
        $this->assertNull($this->sut->hasChangedRegisteredAddress(123, []));
        $this->assertNull($this->sut->hasChangedNatureOfBusiness(123, []));
        $this->assertNull($this->sut->hasChangedSubsidiaryCompany(123, []));
        $this->assertNull($this->sut->postSave([]));
        $this->assertNull($this->sut->postCrudSave('added', []));
    }
}
