<?php


namespace CommonTest\Validation\Result;

use Common\Service\Validation\Result\ValidationFailed;
use Common\Service\Validation\CommandInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Mockery as m;

/**
 * Class PackValidationFailedTest
 * @package OlcsTest\Ebsr\Data\Object\Result
 */
class PackValidationFailedTest extends TestCase
{
    public function testObject()
    {
        $command  = m::mock(CommandInterface::class);
        $sut = new ValidationFailed($command, ['failure' => 'It failed!']);

        $this->assertSame($command, $sut->getCommand());
        $this->assertEquals(['failure' => 'It failed!'], $sut->getMessages());
    }
}
