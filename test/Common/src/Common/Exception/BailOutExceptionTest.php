<?php

namespace CommonTest\Exception;

use Common\Exception\BailOutException;

/**
 * Bail Out Exception Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BailOutExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = new BailOutException('foo', 'bar');

        $this->assertEquals('foo', $exception->getMessage());
        $this->assertEquals('bar', $exception->getResponse());
    }
}
