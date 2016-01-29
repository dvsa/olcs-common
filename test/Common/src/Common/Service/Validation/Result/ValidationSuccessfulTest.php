<?php

namespace CommonTest\Validation\Result;

use Common\Service\Validation\Result\ValidationSuccessful;
use Common\Service\Validation\CommandInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Mockery as m;

/**
 * Class PackValidationSuccessfulTest
 * @package CommonTest\Validation\Result
 */
class ValidationSuccessfulTest extends TestCase
{
    public function testObject()
    {
        $command  = m::mock(CommandInterface::class);
        $sut = new ValidationSuccessful($command, ['results' => 'Something valid'], ['some' => 'context']);

        $this->assertSame($command, $sut->getCommand());
        $this->assertEquals(['results' => 'Something valid'], $sut->getResult());
        $this->assertEquals(['some' => 'context'], $sut->getContext());
    }
}
