<?php

/**
 * Test OperatingCentreCommunityLicences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\InputFilters;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\InputFilters\OperatingCentreCommunityLicences;
use Common\Form\Elements\Validators\OperatingCentreCommunityLicencesValidator;
use Zend\Validator as ZendValidator;

/**
 * Test OperatingCentreCommunityLicences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreCommunityLicencesTest extends PHPUnit_Framework_TestCase
{
    /**+
     * Holds the element
     */
    private $element;

    /**
     * Setup the element
     */
    public function setUp()
    {
        $this->element = new OperatingCentreCommunityLicences();
    }

    /**
     * Test validators
     */
    public function testValidators()
    {
        $spec = $this->element->getInputSpecification();

        $this->assertTrue($spec['validators'][0] instanceof ZendValidator\Digits);
        $this->assertTrue($spec['validators'][1] instanceof ZendValidator\Between);
        $this->assertTrue($spec['validators'][2] instanceof OperatingCentreCommunityLicencesValidator);
    }
}
