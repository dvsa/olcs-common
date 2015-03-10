<?php

/**
 * CPMS Exception Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Cpms;

/**
 * CPMS Exception Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that all the Cpms Exceptions can be instantiated and inherit correctly
     * @dataProvider exceptionProvider
     * @param string $exceptionClass
     */
    public function testInstantiation($exceptionClass, $parentClass = null)
    {
        $className = '\Common\Service\Cpms\Exception\\'.$exceptionClass;
        $ex = new $className;
        $this->assertInstanceOf('\Common\Service\Cpms\Exception', $ex);

        if (!is_null($parentClass)) {
            $expectedParent = '\Common\Service\Cpms\Exception\\'.$parentClass;
            $this->assertInstanceOf($expectedParent, $ex);
        }
    }

    public function exceptionProvider()
    {
        return [
            ['PaymentException'],
            ['PaymentInvalidAmountException', 'PaymentException'],
            ['PaymentInvalidResponseException', 'PaymentException'],
            ['PaymentInvalidStatusException', 'PaymentException'],
            ['PaymentInvalidTypeException', 'PaymentException'],
            ['PaymentNotFoundException', 'PaymentException'],
            ['StatusException'],
            ['StatusInvalidResponseException', 'StatusException'],
        ];
    }
}
