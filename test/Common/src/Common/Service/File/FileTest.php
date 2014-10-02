<?php

/**
 * Test File class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\File;

use Common\Service\File\File;

/**
 * Test File class
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testFromData()
    {
        $data = array(
            'name' => 'Bob',
            'type' => 'image/png',
            'tmp_name' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => 'foo',
            'meta' => [1]
        );

        $expected = array(
            'identifier' => null,
            'name' => 'Bob',
            'type' => 'image/png',
            'path' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => 'foo',
            'meta' => [1]
        );

        $file = new File();
        $file->fromData($data);
        $this->assertEquals($expected, $file->toArray());
    }

    public function testFromDataWithIdentifier()
    {
        $data = array(
            'name' => 'Bob',
            'type' => 'image/png',
            'tmp_name' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646
        );

        $expected = array(
            'identifier' => 'dfghjkl',
            'name' => 'Bob',
            'type' => 'image/png',
            'path' => '/sdflkajdsf/asdfjasldf',
            'size' => 45646,
            'content' => null,
            'meta' => []
        );

        $file = new File();
        $file->setIdentifier('dfghjkl');
        $file->fromData($data);
        $this->assertEquals($expected, $file->toArray());
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGetExtension($name, $extension)
    {
        $file = new File();
        $file->setName($name);

        $this->assertEquals(
            $extension,
            $file->getExtension()
        );
    }

    public function extensionProvider()
    {
        return array(
            array('', ''),
            array('A file with no extension', ''),
            array('Another file.txt', 'txt'),
            array('article.jpg.doc', 'doc')
        );
    }

    public function testGetRealType()
    {
        $file = new File();
        $file->setContent('plain text');

        $this->assertEquals(
            'text/plain',
            $file->getRealType()
        );
    }
}
