<?php

/**
 * File Content Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Util;

use Common\Util\FileContent;

/**
 * File Content Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileContentTest extends \PHPUnit_Framework_TestCase
{
    public function testFileContent()
    {
        $fileContent = new FileContent('foo.pdf');
        $this->assertEquals('foo.pdf', $fileContent->getFileName());
        $this->assertEquals('foo.pdf', (string)$fileContent);
    }
}
