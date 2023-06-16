<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\ConvictionsPenalties;
use Common\FormService\Form\Lva\LicenceHistory;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Laminas\Form\Form;
use Mockery as m;

/**
 * Licence History Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class LicenceHistoryTest extends AbstractLvaFormServiceTestCase
{
    protected $classToTest = LicenceHistory::class;

    protected $formName = 'Lva\LicenceHistory';

    public function setUp(): void
    {
        $this->translator = m::mock(TranslationHelperService::class);
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->classArgs = [$this->translator, $this->urlHelper];
        parent::setUp();
    }
}
