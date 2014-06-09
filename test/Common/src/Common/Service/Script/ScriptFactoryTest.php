<?php

/**
 * ScriptFactory Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Script;

use Common\Service\Script\ScriptFactory;

/**
 * ScriptFactory Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ScriptFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $config = [];

    /**
     * test before hook
     *
     * @return void
     */
    public function setUp()
    {
        $this->config = [
            'local_scripts_path' => __DIR__ . '/TestResources/'
        ];
        $this->service = new ScriptFactory($this->config);
    }

    public function testLoadFileWithNonExistentPath()
    {
        try {
            $this->service->loadFile('/foo/bar');
        } catch (\Exception $e) {
            $this->assertEquals('Attempted to load invalid script file "/foo/bar"', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testLoadFileWithExistentPath()
    {
        $result = $this->service->loadFile('stub');
        $this->assertEquals("alert(\"I am a dummy fixture!\");\n", $result);
    }

    public function testLoadFilesWhereOneOrMoreDoesNotExist()
    {
        try {
            $this->service->loadFiles(['stub', 'foo']);
        } catch (\Exception $e) {
            $this->assertEquals('Attempted to load invalid script file "foo"', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not thrown');
    }

    public function testLoadFilesWhereAllFilesExist()
    {
        $scripts = $this->service->loadFiles(['stub', 'another_stub']);
        $this->assertEquals(
            $scripts,
            [
                "alert(\"I am a dummy fixture!\");\n",
                "alert(\"I am a stub!\");\n"
            ]
        );
    }
}
