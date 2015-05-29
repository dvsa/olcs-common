<?php

/**
 * Test PrivateHireLicenceTrafficAreaValidator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Form\Elements\Validators;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Form\Elements\Validators\PrivateHireLicenceTrafficAreaValidator;
use CommonTest\Bootstrap;

/**
 * Test PrivateHireLicenceTrafficAreaValidator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PrivateHireLicenceTrafficAreaValidatorTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    /**
     * Set up the validator
     */
    public function setUp()
    {
        $this->sut = new PrivateHireLicenceTrafficAreaValidator();
        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test isValid handling of error from postcode service, OLCS-8753
     */
    public function testIsValidHandlesPostcodeServiceError()
    {
        $value = 'foo';

        $mockPostCodeService = m::mock();
        $this->sm->setService('postcode', $mockPostCodeService);

        $mockPostCodeService
            ->shouldReceive('getTrafficAreaByPostcode')
            ->with($value)
            ->once()
            ->andThrow(new \Exception('fail!'));

        $this->assertTrue($this->sut->isValid($value));
    }
}
