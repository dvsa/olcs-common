<?php

/**
 * MultipleFileUploadTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Types\MultipleFileUpload;

/**
 * MultipleFileUploadTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MultipleFileUploadTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the element configuration
     */
    public function testElement()
    {
        $element = new MultipleFileUpload();

        $this->assertEquals('Upload file', $element->getLabel());

        $this->assertTrue($element->has('list'));
        $this->assertTrue($element->has('__messages__'));
        $this->assertTrue($element->has('file-controls'));
        $this->assertTrue($element->get('file-controls')->has('file'));
        $this->assertTrue($element->get('file-controls')->has('upload'));
    }
}
