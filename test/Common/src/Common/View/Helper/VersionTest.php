<?php

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use Common\View\Helper\Version;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Test Version view helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VersionTest extends MockeryTestCase
{
    /**
     * Test render without version
     */
    public function testRenderWithoutVersion(): void
    {
        $config = [];
        $sut = new Version($config);

        $this->assertEquals('', $sut->render());
    }

    /**
     * Test render with version
     */
    public function testRenderWithVersion(): void
    {
        $config = [
            'version' => [
                'environment' => 'Unit Test',
                'description' => 'DESCRIPTION',
                'release' => '1.0'
            ]
        ];
        $sut = new Version($config);

        $expected = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">Unit Test</span></p>
    <p class="version">PHP: <span>' . phpversion() . '</span></p>
    <p class="version">Description: <span>DESCRIPTION</span></p>
    <p class="version">Version: <span>1.0</span></p>
</div>';

        $this->assertEquals($expected, $sut());
    }

    /**
     * Test render with version
     */
    public function testRenderWithoutDetails(): void
    {
        $config = [
            'version' => [
                'environment' => null,
                'release' => null
            ]
        ];
        $sut = new Version($config);

        $expected = '<div class="version-header">
    <p class="environment">Environment: <span class="environment-marker">unknown</span></p>
    <p class="version">PHP: <span>' . phpversion() . '</span></p>
    <p class="version">Description: <span>NA</span></p>
    <p class="version">Version: <span>unknown</span></p>
</div>';

        $this->assertEquals($expected, $sut());
    }
}
