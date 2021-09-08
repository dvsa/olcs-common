<?php

namespace CommonTest\Form\Model\Form\Lva;

use Olcs\TestHelpers\FormTester\AbstractFormValidationTestCase;

/**
 * Class PsvVehiclesVehicleTest
 *
 * @group FormTests
 */
class PsvVehiclesVehicleTest extends AbstractFormValidationTestCase
{
    /**
     * @var string The class name of the form being tested
     */
    protected $formName = \Common\Form\Model\Form\Lva\PsvVehiclesVehicle::class;

    public function testDataId()
    {
        $element = ['data', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testDataVersion()
    {
        $element = ['data', 'version'];
        $this->assertFormElementHidden($element);
    }

    public function testDataVrm()
    {
        $element = ['data', 'vrm'];
        $this->assertFormElementRequired($element, true);
        $this->assertFormElementAllowEmpty($element, false);
        $this->assertFormElementVrm($element);
    }

    public function testDataMakeModel()
    {
        $element = ['data', 'makeModel'];
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);
        $this->assertFormElementText($element, 2, 100);
    }

    public function testLicenceVehicleId()
    {
        $element = ['licence-vehicle','id'];
        $this->assertFormElementHidden($element);
    }

    /**
     * @todo unskip https://jira.dvsacloud.uk/browse/VOL-2309
     */
    public function testLicenceVehicleVersion()
    {
        $this->markTestSkipped();
        $element = ['licence-vehicle','receivedDate'];
        $this->assertFormElementDate($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testLicenceVehicleSpecifiedDateTime()
    {
        $element = ['licence-vehicle', 'specifiedDate'];

        $tomorrow = new \DateTimeImmutable('+1 day');

        $this->assertFormElementDateTimeValidCheck(
            $element,
            [
                'year'   => $tomorrow->format('Y'),
                'month'  => $tomorrow->format('m'),
                'day'    => $tomorrow->format('j'),
                'hour'   => 12,
                'minute' => 12,
                'second' => 12,
            ]
        );
    }

    /**
     * @todo unskip https://jira.dvsacloud.uk/browse/VOL-2309
     */
    public function testLicenceVehicleRemovalDate()
    {
        $this->markTestSkipped();
        $element = ['licence-vehicle','removalDate'];
        $this->assertFormElementDate($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testLicenceVersion()
    {
        $element = ['licence-vehicle','version'];
        $this->assertFormElementHidden($element);
    }

    public function testDiscNumber()
    {
        $element = ['licence-vehicle','discNo'];
        $this->assertFormElementText($element);
        $this->assertFormElementRequired($element, false);
    }

    public function testVehicleHistory()
    {
        $element = ['vehicle-history-table', 'table'];
        $this->assertFormElementTable($element);
        $this->assertFormElementRequired($element, false);
        $this->assertFormElementAllowEmpty($element, true);

        $element = ['vehicle-history-table', 'action'];
        $this->assertFormElementHidden($element);

        $element = ['vehicle-history-table', 'rows'];
        $this->assertFormElementHidden($element);

        $element = ['vehicle-history-table', 'id'];
        $this->assertFormElementHidden($element);
    }

    public function testAddAnother()
    {
        $element = ['form-actions', 'addAnother'];
        $this->assertFormElementActionButton($element);
    }

    public function testSubmit()
    {
        $element = ['form-actions', 'submit'];
        $this->assertFormElementActionButton($element);
    }

    public function testCancel()
    {
        $element = ['form-actions', 'cancel'];
        $this->assertFormElementActionButton($element);
    }
}
