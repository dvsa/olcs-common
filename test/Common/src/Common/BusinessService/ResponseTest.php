<?php

/**
 * Response Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService;

use PHPUnit_Framework_TestCase;
use Common\BusinessService\Response;

/**
 * Response Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Response();
    }

    public function testGetType()
    {
        $this->sut->setType('foo');

        $this->assertEquals('foo', $this->sut->getType());
    }

    public function testGetData()
    {
        $this->sut->setData(['foo']);

        $this->assertEquals(['foo'], $this->sut->getData());
    }

    public function testGetMessage()
    {
        $this->sut->setMessage('foo');

        $this->assertEquals('foo', $this->sut->getMessage());
    }

    /**
     * @dataProvider isOkProvider
     */
    public function testIsOk($type, $expected)
    {
        $this->sut->setType($type);
        $this->assertEquals($expected, $this->sut->isOk());
    }

    public function isOkProvider()
    {
        return [
            [
                Response::TYPE_SUCCESS,
                true
            ],
            [
                Response::TYPE_NO_OP,
                true
            ],
            [
                Response::TYPE_FAILED,
                false
            ],
            [
                Response::TYPE_RULE_FAILED,
                false
            ]
        ];
    }
}
