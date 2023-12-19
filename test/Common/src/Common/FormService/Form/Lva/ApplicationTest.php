<?php

namespace CommonTest\Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\Application;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTest extends MockeryTestCase
{
    protected $sut;

    public function setUp(): void
    {
        $this->formHelper = m::mock(FormHelperService::class);
        $this->authService = m::mock(AuthorizationService::class);
        $this->sut = new Application($this->formHelper, $this->authService);
    }

    /**
     * No op
     */
    public function testAlterForm()
    {
        $form = m::mock('\Laminas\Form\Form');

        $this->assertNull($this->sut->alterForm($form));
    }
}
