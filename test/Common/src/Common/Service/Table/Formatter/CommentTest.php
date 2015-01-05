<?php


namespace CommonTest\Service\Table\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Common\Service\Table\Formatter\Comment;

/**
 * Class CommentTest
 * @package CommonTest\Service\Table\Formatter
 */
class CommentTest extends TestCase
{
    public function testFormat()
    {
        $sut = new Comment();
        $result = $sut->format(
            ['statusField' => "Test \nnote"],
            ['name' => 'statusField', 'formatter' => 'comment']
        );

        $this->assertEquals("Test <br />\nnote", $result);
    }
}
