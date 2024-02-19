<?php

namespace CommonTest\Service\Script;

use Common\Service\Script\ScriptFactory;
use Interop\Container\ContainerInterface;

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

        $valueMap = [
            ['ViewHelperManager', $vhm],
            ['Config', $this->config],
        ];

        $sl = $this->createMock(ContainerInterface::class);
        $sl->expects($this->any())
           ->method('get')
           ->will($this->returnValueMap($valueMap));

        $this->service = new ScriptFactory();
        $this->service->__invoke($sl, ScriptFactory::class);
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
