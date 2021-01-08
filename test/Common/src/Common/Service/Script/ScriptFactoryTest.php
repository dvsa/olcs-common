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
class ScriptFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $config = [];

    /**
     * test before hook
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->config = [
            'local_scripts_path' => [__DIR__ . '/TestResources/']
        ];

        $this->inlineScript = new \Laminas\View\Helper\InlineScript();

        $vhm = $this->createMock('\Laminas\View\HelperPluginManager', ['get']);
        $vhm->expects($this->any())
            ->method('get')
            ->with('inlineScript')
            ->will($this->returnValue($this->inlineScript));

        $valueMap = array(
            array('ViewHelperManager', $vhm),
            array('Config', $this->config),
        );

        $sl = $this->createMock('\Laminas\ServiceManager\ServiceLocatorInterface', ['get', 'has']);
        $sl->expects($this->any())
           ->method('get')
           ->will($this->returnValueMap($valueMap));

        $this->service = new ScriptFactory();
        $this->service->createService($sl);
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
        $this->service->loadFile('stub');
        $jsArray = [];

        foreach ($this->inlineScript as $item) {
            $jsArray[] = $item->source;
        }

        $this->assertEquals(
            $jsArray,
            [
                "alert(\"I am a dummy fixture!\");\n"
            ]
        );
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

        $jsArray = [];

        foreach ($this->inlineScript as $item) {
            $jsArray[] = $item->source;
        }

        $this->assertEquals(
            $jsArray,
            [
                "alert(\"I am a dummy fixture!\");\n",
                "alert(\"I am a stub!\");\n"
            ]
        );
    }
}
