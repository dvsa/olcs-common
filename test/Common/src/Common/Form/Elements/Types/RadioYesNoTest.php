<?php

namespace CommonTest\Form\Elements\Types;

use Common\Form\Elements\Types\RadioYesNo;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

/**
 * RadioYesNoTest
 */
class RadioYesNoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RadioYesNo
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new RadioYesNo();
    }

    public function testInit()
    {
        $this->sut->init();

        Assert::assertArraySubset(
            ['Y' => ['label' => 'Yes', 'value' => 'Y'], 'N' => ['label' => 'No', 'value' => 'N']],
            $this->sut->getValueOptions()
        );
    }
}
