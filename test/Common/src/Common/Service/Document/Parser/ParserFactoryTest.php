<?php

namespace CommonTest\Service\Document\Parser;

use PHPUnit_Framework_TestCase;
use Common\Service\Document\Parser\ParserFactory;

class ParserFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testGetParser($type, $class)
    {
        $factory = new ParserFactory();
        $parser = $factory->getParser($type);

        $this->assertInstanceOf($class, $parser);
    }

    public function testGetParserWithUnknownType()
    {
        $factory = new ParserFactory();

        try {
            $parser = $factory->getParser('unknown');
        } catch (\RuntimeException $e) {
            $this->assertEquals('No parser found for mime type: unknown', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not found');
    }

    public function typeProvider()
    {
        return [
            ['application/rtf', 'Common\Service\Document\Parser\RtfParser'],
            ['application/x-rtf', 'Common\Service\Document\Parser\RtfParser']
        ];
    }
}
