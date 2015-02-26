<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormTest;
use Olcs\TestHelpers\FormTester\Data\Object as F;
use CommonTest\Bootstrap;

/**
 * Class OperatingCentresTest
 *
 * @package OlcsTest\FormTest
 * @group ComponentTests
 * @group FormTests
 */
class OperatingCentresTest extends AbstractFormTest
{
    protected $formName = '\Common\Form\Model\Form\Lva\OperatingCentres';

    protected function getServiceManager()
    {
        return Bootstrap::getRealServiceManager();
    }

    protected function getFormData()
    {
        $smContext    = new F\Context(new F\Stack(['data', 'totAuthSmallVehicles']) , '2');
        $medContext   = new F\Context(new F\Stack(['data', 'totAuthMediumVehicles']), '3');
        $largeContext = new F\Context(new F\Stack(['data', 'totAuthLargeVehicles']) , '4');
        $ocContext    = new F\Context(new F\Stack(['data', 'noOfOperatingCentres']) , '1');
        $minContext   = new F\Context(new F\Stack(['data', 'minVehicleAuth'])       , '9');

        return [
            new F\Test(
                new F\Stack(['data', 'totAuthVehicles']),
                new F\Value(F\Value::VALID, ''),
                new F\Value(F\Value::INVALID, '', $smContext, $medContext, $largeContext, $ocContext, $minContext),
                new F\Value(F\Value::INVALID, '0', $smContext, $medContext, $largeContext, $ocContext, $minContext),
                new F\Value(F\Value::INVALID, '12', $smContext, $medContext, $largeContext, $ocContext, $minContext),
                new F\Value(F\Value::VALID, '9', $smContext, $medContext, $largeContext, $ocContext, $minContext),
                new F\Value(F\Value::INVALID, 'foo'),
                new F\Value(F\Value::INVALID, 'bar', $smContext, $medContext, $largeContext, $ocContext, $minContext)
            ),
        ];
    }
}